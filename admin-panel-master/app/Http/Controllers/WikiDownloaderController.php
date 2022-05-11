<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Asparagus\QueryBuilder; // https://packagist.org/packages/benestar/asparagus
// https://www.youtube.com/watch?v=FnCo0xhGkIU
// https://tyvik.ru/posts/sparql
// https://github.com/freearhey/wikidata
// https://packagist.org/packages/aternus/geonames-client
use App\ParserParam;
use App\Country;
use App\CountryDuplicate;
use App\Cities1000;

use Wikidata\Wikidata;

class WikiDownloaderController extends Controller
{
    /**
     *  Параметры:
     */
    public function __construct()
    {
        $this->endpoint = 'https://query.wikidata.org'; // https://en.wikibooks.org/wiki/SPARQL
        $this->format = 'json';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->endpoint]); // https://github.com/guzzle/guzzle  

        // Предикаты:
        $this->prop = [
            'принадлежит стране' => 'wdt:P17',
            'является' => 'wdt:P31', // частный случай понятия
            'относится к' => 'wdt:P642', // частный случай понятия
            'OpenStreetMap ID' => 'wdt:P402',
            'подскласс от' => 'wdt:P279',
            'все подклассы' => 'wdt:P279*',
        ];
        
       
       
        

        // Свойства:
        $this->labels = [
            'официальное название' => 'wdt:P1448', 
            'короткое название' => 'wdt:P1813', 
            'координаты' => 'wdt:P625',
            'численность населения' => 'wdt:P1082',
            'изображение' => 'wdt:P18',
            'код страны' => 'wdt:Q906278',
            'ISO 3166-1' => 'wdt:P297',
            'является столицей' => 'wdt:P1376',
        ];
        
        
  
        // Страны
        $this->countries = [
            'Россия' => 'wd:Q159',
            'Украина' => 'wd:Q212',
            'Германия' => 'wd:Q183',
        ];
        
        // Города:
        $this->towns = [
            'Севастополь' => 'wd:Q7525',
        ]; 
       
        

        // Населенные пункты:
        $this->localities = [
            'страна' => 'wd:Q6256',
            'суверенное государство' => 'wd:Q3624078',
            'непризнанное либо частично признанное государство' => 'wd:Q15634554',
            'город-миллионер' => 'wd:Q1637706',
            'город > 100к населения' => 'wd:Q1549591',
            'city+town' => 'wd:Q7930989',
            'столица' => 'wd:Q5119',
            'столица P36' => 'wd:P36',
            'столица у' => 'wd:Q65964597',
            'город' => 'wd:Q515',
            'населенный пункт' => 'wd:Q486972',
            'местонахождение правительства' => 'wd:Q1901835',
            'метрополис' => 'wd:Q200250',
            'населённый пункт любого размера' => 'wd:Q486972',
            
            'земля в составе Германии' => 'wd:Q1221156',
            
        ];
        
        
        

        // Префиксы, неймспейсы:
        $this->prefixes = [
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'wd' => 'http://www.wikidata.org/entity/', // Данные объекта
            'wdt' => 'http://www.wikidata.org/prop/direct/', // wiki data type
            'wds' => 'http://www.wikidata.org/entity/statement/',
            
        ];
        
        $this->wikiQuery = new QueryBuilder($this->prefixes);
        
        $this->pagination = true;
        $this->limit = 10;
        $this->offset = 0;
        
        $this->debug = 0;
        
    }

    /**
     *  Запрос данных:
     */
    public function preparedQueries(Request $request)
    {
        set_time_limit(3000);
        
        // Режим отладки кода:
        if($request->has('sparql')) {
            if($request->sparql == true) {
                $this->debug = 1;
            }
        }
        
        // Запрос:
        $method = $request->method;

        // Ограничить количество записей запроса:
        if($request->has('limit')) {
            $this->limit = (int) $request->limit;
        }
        
        // Страница запроса:
        if($request->has('page')) {
            if($request->page > 1) {
                $this->offset = $this->limit * ($request->page - 1);
            } else {
                $this->offset = 0;
            }
        }
        
        $withRequest = [
            'instanceOf',
            'getCountry',
            'getCityProperties',
            'getCityPropertiesV2',
        ];
        
        // Получить код SPARQL:
        if(in_array($method, $withRequest)) {
            $query = $this->$method($request);                
        } else {
            $query = $this->$method();
        }

        // Если включен режим дебага, возвращаем исходный код запроса:
        if($this->debug == 1) {
            return [
                'status' => 200,
                'data' => $query,
                'request' => $request->toArray(),
                'message' => '',
                'limit' => $this->limit,
                'offset' => $this->offset,
                'page' => $request->page,
            ]; 
        }
        
        // Если режим дебага выключен, делаем запрос к викидате:
        $response = $this->client->request(
            'GET', 
            'sparql?format=' . $this->format . '&query=' . urlencode($query),
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );

        // Обработка ответа сервера:
        if($response->getStatusCode() == 200) {
            
            return [
                'status' => 200,
                'data' => json_decode($response->getBody(), true),
                'request' => $request->toArray(),
                'message' => $response->getStatusCode(),
                'limit' => $this->limit,
                'offset' => $this->offset,
                'page' => $request->page,
            ];
            
        } else {
            
            return [
                'status' => 500,
                'data' => json_encode([]),
                'request' => $request->toArray(),
                'message' => $response->getStatusCode(),
                'limit' => $this->limit,
                'offset' => $this->offset,
                'page' => $request->page,
            ];            
        }
        
    }
    
    /**
     *  Запрос данных:
     */
    public function request($query)
    {
        $response = $this->client->request(
            'GET', 
            'sparql?format=' . $this->format . '&query=' . urlencode($query),
             [
                'allow_redirects' => true,
                'timeout' => 2000,
                'http_errors' => false
            ]
        );

        if($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true); 
        } else {
            return $response->getStatusCode();
        }        
        
    }
    
    public function pagination()
    {
        return 'LIMIT ' . $this->limit . ' OFFSET ' . $this->offset;
    }

    /*
        Узнать чем является элемент:
    */
    public function instanceOf(Request $request)
    {
        $this->wikiQuery
            ->select('?item', '?label')
            ->where('wd:' . (string) $request->q, $this->prop['является'], '?item')   
            ->where('?item', 'rdfs:label', '?label')
            ->filter('LANG(?label) = "ru"')
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();
    }    
    
    public function getInstanceOf(Request $request)
    {
        if ($request->isMethod('get')) {
            return view("cp.Wikitest.instance_of", compact(""));
        }
    }
    
    public function getCountryView(Request $request)
    {
        if ($request->isMethod('get')) {
            return view("cp.Wikitest.country", compact(""));
        }
    }
    
    /*
        Узнать каким странам принадлежит:
    */
    public function getCountry(Request $request)
    {

        $this->wikiQuery
            ->select('?item', '?label')
            ->where('wd:' . (string) $request->q, 'wdt:P17', '?item')   
            ->where('?item', 'rdfs:label', '?label')
            ->filter('LANG(?label) = "ru"')
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();
    }
    
    public function getCountrySource(Request $request)
    {

         
        
        $this->wikiQuery
            ->selectDistinct(
                '?item', 
                '?name'
            )  
            ->union(
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['страна'])
                    ->where('?item', 'rdfs:label', '?name'),
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['суверенное государство'])
                    ->where('?item', 'rdfs:label', '?name')                  
            )  
            ->filter('LANG(?name) = "ru"')
            ->orderBy('?name')
            ->offset($this->offset)
            ->limit($this->limit);

         echo $this->wikiQuery->format();  

    }
    
    /*
        Узнать свойства населенного пункта:
    */
    public function getCityProperties(Request $request)
    {
        
        if($this->debug == 1) {
            if($request->has('q')) {
                if(strlen($request->q) == 0 ) {
                    return 'Введите идентификатор города с префиксом Q!';
                }
            }
        }
        
        $humanSettlements = [
            'Q5119'         => '# Столица',
            'Q5124045'      => '# Город со специальным статусом',
            'Q51929311'     => '# первый по величине город',
            'Q7930989'      => '# город',
            'Q1637706'      => '# город-миллионер',
            'Q1221156'      => '# земля в германии',
            'Q1093829'      => '# большой город в США',
            'Q1549591'      => '# город с населением более 100 000 человек',
            'Q183342'       => '# город федерального значения в России',
            'Q3257686'      => '# обжитая местность',
            'Q3957'         => '# малый город',
            'Q26714626'     => '# ип поселения, меньше, чем город',
        ];
        
        $fields = [
            '?coordinates',
            '?population',
            '?label_ru',
            '?label_en',
            '?country_code',
        ];
        
        $postFix = '_field';
                
        
        $string  = '';
        $string .= 'SELECT ?item ' . PHP_EOL;
        
        foreach($fields as $field) {
            $string .= '(SAMPLE('. $field . $postFix . ') AS '. $field .') ' . PHP_EOL;
        }
        
        foreach($fields as $field) {
            //$string .= $field.' ' . PHP_EOL;
        }
        
        $string .= 'WHERE' . PHP_EOL;
        $string .= '{' . PHP_EOL;
        $string .= '    FILTER (?population' . $postFix . ' > 0) {' . PHP_EOL;
        $string .= '    }' . PHP_EOL;
        
        foreach($humanSettlements as $Q => $comment) {
             $string .= 'UNION {'. PHP_EOL;
             $string .= '   ?item wdt:P31 wd:' . $Q . ' .'. PHP_EOL;
             $string .= '    '. PHP_EOL;
             $string .= '    VALUES ?item { wd:' . (string) $request->q . ' }'. PHP_EOL;
             $string .= '    '. PHP_EOL;
             $string .= '        OPTIONAL {'. PHP_EOL;
             $string .= '            ?item wdt:P625 '.$fields[0]. $postFix . ';'. PHP_EOL;
             $string .= '            wdt:P1082 '.$fields[1]. $postFix . ';'. PHP_EOL;
             $string .= '            wdt:P17 ?country.'. PHP_EOL;
             $string .= '            ?country wdt:P297 '.$fields[4]. $postFix . '.'. PHP_EOL;
             $string .= '        }'. PHP_EOL;
             $string .= '    '. PHP_EOL;
             $string .= '    ?item rdfs:label '.$fields[2]. $postFix . ' filter (lang('.$fields[2]. $postFix . ') = "ru").'. PHP_EOL;
             $string .= '    ?item rdfs:label '.$fields[3]. $postFix . ' filter (lang('.$fields[3]. $postFix . ') = "en").'. PHP_EOL;
             $string .= '}   '. PHP_EOL;
        
        }
        $string .= '}    '. PHP_EOL;
        $string .= 'GROUP BY ?item '. PHP_EOL;
        $string .= $this->pagination();       
        
        return $string;
        
    }
    
    /*
        Узнать свойства населенного пункта:
    */
    public function getCityPropertiesV2(Request $request)
    {
        if($this->debug == 1) {
            if($request->has('q')) {
                if(strlen($request->q) == 0 ) {
                    return 'Введите идентификатор города с префиксом Q!';
                }
            }
        }

        $humanSettlements = [
            'Q515'         => ' # Город',
            'Q5119'         => '# Столица',
            'Q5124045'      => '# Город со специальным статусом',
            'Q51929311'     => '# первый по величине город',
            'Q7930989'      => '# город',
            'Q1637706'      => '# город-миллионер',
            'Q1221156'      => '# земля в германии',
            'Q1093829'      => '# большой город в США',
            'Q1549591'      => '# город с населением более 100 000 человек',
            'Q183342'       => '# город федерального значения в России',
            'Q3257686'      => '# обжитая местность',
            'Q3957'         => '# малый город',
            'Q26714626'     => '# тип поселения, меньше, чем город',
            'Q12131640'     => '# город районного значения',
            'Q200250'     =>   '# метрополис',
        ];
        
        $fields = [
            '?coordinates',
            '?population',
            '?label_ru',
            '?label_en',
            '?country_code',
        ];
        
        $postFix = '_field';
                
        
        $string  = '';
        $string .= 'SELECT ?item ' . PHP_EOL;
        
        foreach($fields as $field) {
            $string .= '(SAMPLE('. $field . $postFix . ') AS '. $field .') ' . PHP_EOL;
        }
        
        foreach($fields as $field) {
            //$string .= $field.' ' . PHP_EOL;
        }
        
        $string .= 'WHERE' . PHP_EOL;
        $string .= '{' . PHP_EOL;
        $string .= '    VALUES ?townType'. PHP_EOL;
        $string .= '    {'. PHP_EOL;
        
        foreach($humanSettlements as $Q => $comment) {
            $string .= '       wd:'. $Q . ' ' . $comment . PHP_EOL;
        }
        $string .= '    }'. PHP_EOL;
        $string .= '    '. PHP_EOL;

        //$string .= '    ?item wdt:P31 ?townType .'. PHP_EOL;
        $string .= '    ?item (wdt:P31/(wdt:P279*)) ?townType .'. PHP_EOL;
        $string .= '    '. PHP_EOL;
        $string .= '    VALUES ?item { wd:' . (string) $request->q . ' }'. PHP_EOL;
        $string .= '    '. PHP_EOL;
        $string .= '        OPTIONAL {'. PHP_EOL;
        $string .= '            ?item wdt:P625 '.$fields[0]. $postFix . ';'. PHP_EOL;
        $string .= '            wdt:P1082 '.$fields[1]. $postFix . ';'. PHP_EOL;
        $string .= '            wdt:P17 ?country.'. PHP_EOL;
        $string .= '            ?country wdt:P297 '.$fields[4]. $postFix . '.'. PHP_EOL;
        $string .= '        }'. PHP_EOL;
        $string .= '    '. PHP_EOL;
        $string .= '    ?item rdfs:label '.$fields[2]. $postFix . ' filter (lang('.$fields[2]. $postFix . ') = "ru").'. PHP_EOL;
        $string .= '    ?item rdfs:label '.$fields[3]. $postFix . ' filter (lang('.$fields[3]. $postFix . ') = "en").'. PHP_EOL;
        $string .= '}    '. PHP_EOL;
        $string .= 'GROUP BY ?item '. PHP_EOL;
        $string .= $this->pagination();       
        
        return $string;
        
    }
    
    public function cityProperties(Request $request)
    {
        if ($request->isMethod('get')) {
            return view("cp.Wikitest.city_properties", compact(""));
        }
    }
    
    
    /**
     *  Обработка тестового запроса:
     */
    public function index()
    {
        // $query = $this->getSomething3();
        // $query = $this->germanCities();         
        // $query = $this->capital($this->countries['Германия']);  
        // $query = $this->germanCities();
        $query = $this->countriensWithMillionaries();
        
        if($this->debug == 1) {
            echo '<pre>' . PHP_EOL . $query;
            dd();  
        }

        $response = $this->request($query);

        if($response == 400) {
            
            return response()->json([
                'status' => 400,
                'message' => 'Bad request.'
            ]);
            
        } else if($response == 500) {
            
            return response()->json([
                'status' => 500,
                'message' => 'Remote server error.'
            ]);
            
        } else {
            return response()->json($response);
        }

    }

    public function downloadCountries()
    {
        $currentPage = $this->currentCountriesPage();
        return view("cp.Wikitest.download_countries", compact('currentPage'));
    }
    
    public function getContriesCount(Request $request)
    {
        return response()->json([
            'status' => 200,
            'count' => $this->preparedQueries($request),
        ]);
    }
    
    public function currentCountriesPage()
    {
        $ContriesLastPage = ParserParam::where('param_name', 'ContriesLastPage')->first();
        
        if($ContriesLastPage == null) {
            $ContriesLastPage = new ParserParam;
            $ContriesLastPage->param_name = 'ContriesLastPage';
            $ContriesLastPage->param_value = 1;
            $ContriesLastPage->save();
        }
        
        
        return $ContriesLastPage->param_value;
    }
    
    public function getContriesLastPage()
    {        
        return response()->json([
            'status' => 200,
            'current_page' => $this->currentCountriesPage(),
        ]);
    }
    
    public function saveContriesLastPage(Request $request)
    {
        $ContriesLastPage = ParserParam::where('param_name', 'ContriesLastPage')->first();
        
        if($ContriesLastPage == null) {
            $ContriesLastPage = new ParserParam;
            $ContriesLastPage->param_name = 'ContriesLastPage';
            $ContriesLastPage->param_value = $request->page;
            $ContriesLastPage->save();
        }
        
        $ContriesLastPage->param_value = $request->page;
        $ContriesLastPage->save();
        
        return response()->json([
            'status' => 200,
            'current_page' => $ContriesLastPage->param_value,
        ]);
    }
    
    public function contriesParsePage(Request $request)
    {
        return response()->json($this->preparedQueries($request));
    }
   
    /**
     *  Возвращает список стран:
     */
    public function allCountries()
    {
        // $this->debug = 1;
        /*
        $this->wikiQuery
            ->selectDistinct(
                '?item', 
                '?name'
            )  
            ->union(
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['страна'])
                    ->where('?item', 'rdfs:label', '?name'),
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['суверенное государство'])
                    ->where('?item', 'rdfs:label', '?name')                  
            )  
            ->filter('LANG(?name) = "ru"')
            ->orderBy('?name')
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();  */
        
return 'SELECT DISTINCT ?item ?name ?code WHERE {
	FILTER (LANG (?name) = "ru") {
		?item wdt:P31 wd:Q6256 ;
			rdfs:label ?name ;
            wdt:P297   ?code
	} UNION {
		?item wdt:P31 wd:Q3624078 ;
			rdfs:label ?name ;
            wdt:P297   ?code
	}
}
ORDER BY ASC (?name)
LIMIT '.$this->limit.' OFFSET ' . $this->offset;
        
        
    } 

    public function allCountriesCount()
    {
        // ( COUNT( DISTINCT ?item ) AS ?HowManyTriples )
        return
'SELECT ( COUNT( DISTINCT ?item ) AS ?HowManyTriples ) WHERE {
	FILTER (LANG (?name) = "ru") {
		?item wdt:P31 wd:Q6256 ;
			rdfs:label ?name ;
            wdt:P297   ?code
	} UNION {
		?item wdt:P31 wd:Q3624078 ;
			rdfs:label ?name ;
            wdt:P297   ?code
	}
}
ORDER BY ASC (?name)';

    } 
    
    
    public function addNewCountry(Request $request)
    {
        $country = Country::where('wiki_id', $request->wiki_id)->first();
        
        // Дубликат:
        if($country != null) {
            // $CountryDuplicate = CountryDuplicate::where('wiki_id', $request->wiki_id)->first();
            return response()->json([
                'status' => 200,
                'duplicate' => true,
                'country' => $country,
            ]);
        }
        
        // Добавляем запись:
        $country = new Country;
        $country->fill($request->all());
        $country->save();
        
        return response()->json([
            'status' => 200,
            'duplicate' => false,
            'country' => $country,
        ]);
    }
    
    public function apiCountry(Request $request)
    {
        $country = Country::where('moderated', $request->moderated)->orderBy('id', 'desc')->paginate($request->limit);
        return response()->json([
            'status' => 200,
            'data' => $country

        ]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function checkEntityDuplicates()
    {
        $q = [];
        $cities = Cities1000::whereNotNull('wiki_entity')->get();
        
        foreach($cities as $city) {
            if(array_key_exists($city->wiki_entity, $q)) {
                $q[$city->wiki_entity] += 1;
            } else {
                $q[$city->wiki_entity] = 1;
            }
        }
        
        foreach($q as $entity => $count) {
            if($count < 2) {
                unset($q[$entity]);
            }
        }
        /*
        $codes_to_delete = [
            //"PPL" =>"населенный пункт",
            //"PPLA" =>"центр административного деления первого порядка",
           // "PPLC" =>"столица политического образования",
            "PPLA2" =>"центр административного деления второго порядка",
            "PPLW" =>"разрушенный населенный пункт",
            "PPLA3" =>"центр административного деления третьего порядка",
            "PPLX" =>"участок населенного пункта",
            "PPLA4" =>"центр административного деления четвертого порядка",
            //"PPLL" =>"населенный пункт",
            //"PPLS" =>"населенные пункты",
            "PPLQ" =>"покинутый населенный пункт",
            //"PPLF" =>"деревня",
            "PPLA5" =>"seat of a fifth-order administrative division",
            "PPLG" =>"здание правительства политического образования",
            "PPLH" =>"historical populated place",
            "PPLCH" =>"historical capital of a political entity",
            "PPLR" =>"религиозная община",
            "STLMT" =>"еврейские поселения",
        ];

        foreach($q as $entity => $count) {
            $dup_cities = Cities1000::where('wiki_entity', $entity)->get();
            if(count($dup_cities) == 2) {
                
                foreach($dup_cities as $dup_city) {
                    
                    if($dup_city->population < 10000) {
                        $dup_city->delete();
                    }
                    
                    foreach($codes_to_delete as $code => $str) { 
                        if($dup_city->feature_code == $code) {
                            $dup_city->delete();
                        }
                    }
                }
            }
        }
        */
        dd(count($cities), $q);
        
    }
    
    public function findByEntity($entity, $lang = 'en')
    {
        $wikidata = new Wikidata();
        return $wikidata->get($entity, $lang);
    }
    
    public function getCityForSync(Request $request)
    {

        // Беру 1 город:
        $city = Cities1000::where('wiki_sync', 0)->whereNotNull('wiki_entity')->orderBy('population', 'desc')->first();
        
        // Если у него отсутствует хотя бы одно поле:
        if($city->name == null or $city->iata_code == null) {
            
            // Загружаю его данные с вики:
            $entity = null;
            
            try {
                $entity = $this->findByEntity($city->wiki_entity, 'en');
            } catch (\Exception $e) {
                // return $e->getMessage();
            }
            
            if($entity != null) {
                dd($city, $entity);
                
                $entity->label;
            }

        } else {
            $city->wiki_sync = 1;
            $city->save();
            
            return response()->json([
                'status' => 200,
                'data' => $city,
                'message' => 'wiki_sync',
            ]);
        }
        
        
        
    }
    
    
    
    
}

