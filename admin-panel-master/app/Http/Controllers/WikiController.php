<?php 
/*
|-------------------------------------------------------------
| Author: Vlad Salabun 
| Site: https://salabun.com 
| Contacts: https://t.me/vlad_salabun | vlad@salabun.com 
|-------------------------------------------------------------
*/

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
// http://www.geonames.org/export/codes.html
class WikiController extends Controller
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
        
        if ($request->isMethod('get')) {
            return view("cp.Wikitest.index", compact(""));
        }
        
        if ($request->isMethod('post')) {
            
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
                return response()->json([
                    'status' => 200,
                    'data' => $query,
                    'request' => $request->toArray(),
                    'message' => '',
                    'limit' => $this->limit,
                    'offset' => $this->offset,
                    'page' => $request->page,
                ]); 
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
                
                return response()->json([
                    'status' => 200,
                    'data' => json_decode($response->getBody(), true),
                    'request' => $request->toArray(),
                    'message' => $response->getStatusCode(),
                    'limit' => $this->limit,
                    'offset' => $this->offset,
                    'page' => $request->page,
                ]);
                
            } else {
                
                return response()->json([
                    'status' => 500,
                    'data' => json_encode([]),
                    'request' => $request->toArray(),
                    'message' => $response->getStatusCode(),
                    'limit' => $this->limit,
                    'offset' => $this->offset,
                    'page' => $request->page,
                ]);            
            }
            
            
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

    public function germanCities()
    {
        $this->wikiQuery
            ->selectDistinct('?item', '?name', '?population') 
            ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])            
            ->where(               
               '?item', 
                $this->prop['является'] .'/'. $this->prop['все подклассы'],
                $this->localities['город']
            )
            ->where('?item', 'rdfs:label', '?name') 
            ->optional('?item', $this->labels['численность населения'], '?population' )
            ->filter('LANG(?name) = "ru"')  
            ->orderBy('?name')          
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();  
    }
    
    public function capital($country)
    {
        $this->wikiQuery
            ->selectDistinct('?item', '?name', '?population', '?country', '?country_code', '?capital_of') 
            ->where('?item', $this->prop['принадлежит стране'], $country)            
            ->where('?item', $this->prop['является'], $this->localities['столица'])
            ->where('?item', 'rdfs:label', '?name') 
            ->optional('?item', $this->labels['численность населения'], '?population' )
            ->optional('?item', $this->prop['принадлежит стране'], '?country')
            ->optional('?country', $this->labels['ISO 3166-1'], '?country_code' )
            ->optional('?item', $this->labels['является столицей'], '?capital_of' )
            ->filter('LANG(?name) = "ru"') 
            ->offset($this->offset)
            ->limit($this->limit);
            
        return $this->wikiQuery->format();  
    }
    
    
    /**
     *  Возвращает список стран:
     */
    public function allCountries()
    {
        // $this->debug = 1;
        
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
                    ->where('?item', 'rdfs:label', '?name'),
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['непризнанное либо частично признанное государство'])
                    ->where('?item', 'rdfs:label', '?name')                    
            )  
            ->filter('LANG(?name) = "ru"')
            ->orderBy('?name')
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();  
    }
    
    /**
     *  Возвращает список стран:
     */
    public function allCountrieWithCodes()
    {
        // $this->debug = 1;
        
        $this->wikiQuery
            ->selectDistinct(
                '?item', 
                '?name',
                '?country_code'
            )  
            ->union(
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['страна'])
                    ->where('?item', 'wdt:P297', '?country_code')
                    ->where('?item', 'rdfs:label', '?name'),
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['суверенное государство'])
                    ->where('?item', 'wdt:P297', '?country_code')
                    ->where('?item', 'rdfs:label', '?name'),
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['непризнанное либо частично признанное государство'])
                    ->where('?item', 'wdt:P297', '?country_code')
                    ->where('?item', 'rdfs:label', '?name')                    
            )  
            ->filter('LANG(?name) = "ru"')
            ->orderBy('?name')
            ->offset($this->offset)
            ->limit($this->limit);

        return $this->wikiQuery->format();  
    }
   
   
   
    public function germany()
    {
        // $this->debug = 1;
        $this->wikiQuery
            ->selectDistinct(
                '?item',
                '?country',
                '?name',
                '?population',
                '?country_code'
            )  
            ->union(
            
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['город-миллионер'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->optional('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code' ),
                
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['city+town'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->optional('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code' ),

                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->labels['является столицей'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->optional('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code' ),
                    
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['населённый пункт любого размера'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->optional('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code' ),
                    
                $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['земля в составе Германии'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->optional('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code' )    
                    
            )
            
            
            ->filter('LANG(?name) = "ru"')
            ->filter('?country_code = "DE"')
            ->filter('?population > 1000000')
            ->limit($this->limit)
            ->offset($this->offset);

        return $this->wikiQuery->format();  
    }
    
    public function germanyBigCities()
    {
        // $this->debug = 1;
        $this->wikiQuery
            ->select(
                '?item',
                '?name'
               // '?population'
            )  
            ->where('?item', 'wdt:P31/wdt:P279*', 'wd:Q515')
            ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
            ->optional('?item', 'rdfs:label', '?name')               

            ->filter('LANG(?name) = "ru"')
            ->orderBy('?name')
            ->limit($this->limit)
            ->offset($this->offset);

        return $this->wikiQuery->format();  
    }
    
    
    public function berlin()
    {
        // $this->debug = 1;
        $this->wikiQuery
            ->selectDistinct(
                '?item',
                '?country',
                '?name',
                '?population',
                '?country_code'
            )  
            ->union(
               
                 $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['город-миллионер'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->where('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code')
                    ->filter('LANG(?name) = "ru"')
                    ->filter('?country_code = "DE"')
                    ->filter('?population > 1000000')
                 ,
                 
                 $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'], $this->localities['метрополис'])
                    ->where('?item', 'rdfs:label', '?name')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code')            
                    ->filter('LANG(?name) = "ru"')
                    ->filter('?country_code = "DE"')
                    ->filter('?population > 1000000')
                    
            )

            ->limit($this->limit)
            ->offset($this->offset);

            
        return $this->wikiQuery->format();  
    }

    public function obamaChildren()
    {
        return '
SELECT DISTINCT ?child ?childLabel ?genderLabel ?birth_date ?date_of_death
WHERE
{
    ?child wdt:P22 wd:Q76.# ?child  has father   Obama
    OPTIONAL{ ?child wdt:P21 ?gender. }
    OPTIONAL{ ?child wdt:P569 ?birth_date. }
    OPTIONAL{ ?child wdt:P570 ?date_of_death. }
    SERVICE wikibase:label { bd:serviceParam wikibase:language "en". }
} 
' . $this->pagination();

    }

    public function countriensWithCapitals()
    {
        //$this->debug = 1;
        
        return '
SELECT DISTINCT ?country ?countryLabel ?capital ?capitalLabel
WHERE
{
    ?country wdt:P31 wd:Q3624078 .
    #not a former country
    FILTER NOT EXISTS {?country wdt:P31 wd:Q3024240}
    #and no an ancient civilisation (needed to exclude ancient Egypt)
    FILTER NOT EXISTS {?country wdt:P31 wd:Q28171280}
    OPTIONAL { ?country wdt:P36 ?capital } .

    SERVICE wikibase:label { bd:serviceParam wikibase:language "ru" }
} 
ORDER BY ?countryLabel
' . $this->pagination();  
    }

    public function countriensWithCapitalsAndPopulation()
    {
        //$this->debug = 1;
        
        return '
SELECT DISTINCT ?country ?countryLabel ?capital ?capitalLabel ?population
WHERE
{
    ?country wdt:P31 wd:Q3624078 .
    #not a former country
    FILTER NOT EXISTS {?country wdt:P31 wd:Q3024240}
    #and no an ancient civilisation (needed to exclude ancient Egypt)
    FILTER NOT EXISTS {?country wdt:P31 wd:Q28171280}
    OPTIONAL { 
        ?country wdt:P36 ?capital . 
        ?capital ' . $this->labels['численность населения'] . ' ?population 
    } .
    SERVICE wikibase:label { bd:serviceParam wikibase:language "ru" }
} 
ORDER BY ?countryLabel
' . $this->pagination();  
    }
    
    public function countriensWithMillionaries()
    {
        //$this->debug = 1;
        
        return '
SELECT ?city ?cityLabel ?country ?countryLabel ?population
WHERE
{
    ?city wdt:P1082 ?population .

    FILTER(?population>1000000)

    ?city wdt:P31 wd:Q515;
        wdt:P17 ?country
    SERVICE wikibase:label { bd:serviceParam wikibase:language "ru" }
} ORDER BY ?countryLabel
' . $this->pagination();
        
    }

    public function whyNoBerlin()
    {
        return 
'SELECT DISTINCT * WHERE {
  ?city_id (wdt:P31/(wdt:P279*)) '.$this->localities['земля в составе Германии'].';
    wdt:P17 ?country_id;
    wdt:P1082 ?population;
    wdt:P625 ?coordinates;
    #wdt:P18 ?pic_url;
    rdfs:label ?city_en, ?city_ru.
  ?country_id wdt:P297 ?country_code.
  OPTIONAL {
    
  }
  FILTER(((LANG(?city_en)) = "en") && ((LANG(?city_ru)) = "ru"))
  FILTER(?country_code = "DE")
  FILTER(?population >= 1000000)
}'
. $this->pagination();
    }
    
    public function whyNoBerlinORM()
    {
        $this->wikiQuery
            ->selectDistinct(
                '?item',
                '?country',
                '?population',
                '?country_code',
                '?city_en',
                '?city_ru'
            )  
            ->union(
               
                 $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'] .'/'. $this->prop['все подклассы'], $this->localities['город-миллионер'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->where('?item', 'rdfs:label', '?city_en')
                    ->where('?item', 'rdfs:label', '?city_ru')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code')
                    ->filter('LANG(?city_en) = "en" && LANG(?city_ru) = "ru"')
                    ->filter('?country_code = "DE"')
                    ->filter('?population > 1000000')
                 ,
                 
                 $this->wikiQuery->newSubgraph()
                    ->where('?item', $this->prop['является'] .'/'. $this->prop['все подклассы'], $this->localities['город'])
                    ->where('?item', $this->prop['принадлежит стране'], $this->countries['Германия'])
                    ->where('?item', 'rdfs:label', '?city_en')
                    ->where('?item', 'rdfs:label', '?city_ru')
                    ->optional('?item', $this->labels['численность населения'], '?population' )
                    ->optional('?country', 'wdt:P297', '?country_code')            
                    ->filter('LANG(?city_en) = "en" && LANG(?city_ru) = "ru"')
                    ->filter('?country_code = "DE"')
                    ->filter('?population > 1000000')
                    
            )

            ->limit($this->limit)
            ->offset($this->offset);
            
         return $this->wikiQuery->format();  
    }
 
 
    public function allCapitals()
    {
        $this->wikiQuery
            ->selectDistinct(
                '?item',
                '?city_ru'
            ) 
            ->where('?item', $this->prop['является'], $this->localities['столица'])
            ->optional('?item', 'rdfs:label', '?city_ru')
            ->limit($this->limit)
            ->offset($this->offset);
            
        return $this->wikiQuery->format();  

    }
    
    public function LargestCitiesOfTheWorld()
    {
        return
'SELECT DISTINCT ?cityLabel ?population
WHERE
{
  FILTER(?population >= 1000000) {
      ?city '. $this->prop['является'] .'/'. $this->prop['все подклассы'] .' '. $this->localities['город'] .' .
      ?city wdt:P1082 ?population .
      SERVICE wikibase:label {
        bd:serviceParam wikibase:language "ru" .
      }
  }
  
}
ORDER BY (?cityLabel) ' . $this->pagination();
    }
    


    public function russianTownCodes()
    {
        return
'SELECT ?item ?label ?code WHERE {
    ?item wdt:P17 wd:Q159.
    ?item wdt:P31 ?type.
    ?type wdt:P279* wd:Q486972.
    ?item wdt:P473 ?code.
    ?item rdfs:label ?label filter (lang(?label) = "ru").
} ' . $this->pagination();
    }
    
    public function sevastopol()
    {
        /*
        $this->wikiQuery
            ->select('?label', '?population')
            ->where($this->towns['Севастополь'], 'rdfs:label', '?label')   
            ->optional($this->towns['Севастополь'], $this->labels['численность населения'], '?population' )
            //->optional($this->towns['Севастополь'], 'rdfs:label', '?city_ru')
            //->optional($this->towns['Севастополь'], 'rdfs:label', '?city_en')
            ->filter('langMatches( lang(?label), "RU" )')  
           // ->optional('SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE]". }')  
        
            ->offset($this->offset)
            ->limit($this->limit);

            
        return $this->wikiQuery->format(); 
        */
        /*
        return
'SELECT ?label ?population WHERE {
	wd:Q7525 rdfs:label ?label .
	OPTIONAL {
		wd:Q7525 wdt:P1082 ?population .
	}
	FILTER (langMatches (lang (?label), "RU" ))
    SERVICE wikibase:label { bd:serviceParam wikibase:language "[en]". }
} ' . $this->pagination();
    */ 
        return
'SELECT ?item ?label ?population WHERE {
	'.$this->towns['Севастополь'].' '.$this->prop['является'].' ?item 
  
} ' . $this->pagination();
    
    }
        
    public function getAllCitiesInTheWorld()
    {
        // Найти все возможные населенные пункты в мире
        // Прочитать их координаты и лейбл
        // Если нет координат - сообщить
        // Сравнить с существующими записями в БД и взять ближайшую по локации запись
        // Если не найдено, или найдено более, чем 1 - сообщить
    }
    
    
}


/*
    https://www.mediawiki.org/wiki/Wikibase/Indexing/RDF_Dump_Format#Items 
    
    PREFIX wd: <http://www.wikidata.org/entity/>
    PREFIX wds: <http://www.wikidata.org/entity/statement/>
    PREFIX wdv: <http://www.wikidata.org/value/>
    PREFIX wdt: <http://www.wikidata.org/prop/direct/>
    PREFIX wikibase: <http://wikiba.se/ontology#>
    PREFIX p: <http://www.wikidata.org/prop/>
    PREFIX ps: <http://www.wikidata.org/prop/statement/>
    PREFIX pq: <http://www.wikidata.org/prop/qualifier/>
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
    PREFIX bd: <http://www.bigdata.com/rdf#>

    PREFIX wdref: <http://www.wikidata.org/reference/>
    PREFIX psv: <http://www.wikidata.org/prop/statement/value/>
    PREFIX psn: <http://www.wikidata.org/prop/statement/value-normalized/>
    PREFIX pqv: <http://www.wikidata.org/prop/qualifier/value/>
    PREFIX pqn: <http://www.wikidata.org/prop/qualifier/value-normalized/>
    PREFIX pr: <http://www.wikidata.org/prop/reference/>
    PREFIX prv: <http://www.wikidata.org/prop/reference/value/>
    PREFIX prn: <http://www.wikidata.org/prop/reference/value-normalized/>
    PREFIX wdno: <http://www.wikidata.org/prop/novalue/>
    PREFIX wdata: <http://www.wikidata.org/wiki/Special:EntityData/>

    PREFIX schema: <http://schema.org/>
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX owl: <http://www.w3.org/2002/07/owl#>
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
    PREFIX prov: <http://www.w3.org/ns/prov#>
    PREFIX bds: <http://www.bigdata.com/rdf/search#>
    PREFIX gas: <http://www.bigdata.com/rdf/gas#>
    PREFIX hint: <http://www.bigdata.com/queryHints#>
    
    items are prefixed with Q (e.g. Q12345),
    properties are prefixed by P (e.g. P569) and // https://www.wikidata.org/wiki/Special:ListProperties
    lexemes are prefixed by L (e.g. L1).
    
    
   
   
   
--------------------------------------- 
   
SELECT * WHERE
{
  { ?s _:prop "v1" } UNION { ?s _:prop "v2" }

  # Use ?s in other patterns
}

---------------------------------------

SELECT * WHERE
{
  ?s _:prop ?value .
  FILTER(?value IN ("v1", "v2"))

  # Use ?s in other patterns
}   

---------------------------------------

SELECT * WHERE
{
  VALUES (?value) { ( "v1" ) ( "v2 " ) }
  ?s _:prop ?value .

  # Use ?s in other patterns
}

---------------------------------------

SELECT *
WHERE
{
    VALUES ?value
    {
       "value1"
       "value2"
       "etc"
    }

    ?s ?p ?value
}
  
---------------------------------------
  
*/

