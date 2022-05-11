<?php 

namespace App\Http\Controllers\Parser\RegionsByDescription;

use App\Http\Controllers\Parser\ParserController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Carbon\Carbon;
use morphos\Russian\GeographicalNamesInflection;

use App\Country;
use App\Region;
use App\City;



class RegionsByDescriptionController extends ParserController
{
    
    public $checkCityCount = 0;
    public $regionMatches = [];
    
    public $output = '';
    public $keywords = [
        'RU' => [],
        'US' => [],
    ];
    
    // В нижнем регистре:
    public $stopWords = [
        'RU' => ['автономный', 'округ', 'область', 'ао', 'край', 'республика', 'югра', '(', ')', '—', '  '],
        'US' => ['d.c.', 'oblast', 'krai', 'republic', ' of ', ',']
    ];
    
    public function __construct()
    {
        $this->output = new \Symfony\Component\Console\Output\ConsoleOutput();
        
        // $output->writeln('<info>foo</info>');
        // $this->output->writeln("<comment></comment>");
        // $this->output->writeln("my message");
        // $this->output->writeln("<info>my message</info>");
        // $this->output->writeln("<question>foo</question>");        
    }
    
    
    public function run()
    {
        $this->output->writeln('<bg=yellow;options=bold>Начинаю парсинг описаний городов...</>');
        $this->setCountriesCodes(['RU', 'US']);
        $this->getRegions();
        $this->getCities();
        $this->getRegionKeyWords();
        $this->matchCities();
        
    }
    
    public function setCountriesCodes($countriesCodesArray)
    {
        $this->output->writeln(''); 
        $array = [];
        $countries = [];
        
        foreach($countriesCodesArray as $countryCode) {
            $country = Country::where('code', $countryCode)->first();
            
            if($country == null) {
                $this->output->writeln('<error>Не найдено страны с кодом: '. $countryCode. '</error>');
            } else {
                $countries[] = $country->name_ru;
                $array[] = $country;
            }
        }
        
        $this->countries = $array;
        $this->output->writeln('Найдено стран: ' . count($array) . ' шт. (' . implode(',', $countries) . ')');        
        
        return $this;
    }
    
    public function getRegions()
    {
        $this->output->writeln(''); 
        $array = [];
        
        foreach($this->countries as $key => $country) {
            $country->regions = Region::where('country_id', $country->id)->get();
            $this->countries[$key] = $country;
            $this->output->writeln('Найдено регионов (' . $country->code . '): ' . count($country->regions) . ' шт.'); 
        }
        
        return $this;
    }
    
    public function getCities()
    {
        $this->output->writeln(''); 
        $array = [];
        
        foreach($this->countries as $key => $country) {
            $country->cities = City::where('country_code', $country->code)->get();
            $this->countries[$key] = $country;
            $this->output->writeln('Найдено городов (' . $country->code . '): ' . count($country->cities) . ' шт.'); 
        }
        
        return $this;
    }
    
    public function getRegionKeyWords()
    {
        
        foreach($this->countries as $key => $country) {
            foreach($country->regions as $key2 => $region) { 
                $this->prepareKeyword($country, $region);                
                
            }
            
        }

        foreach($this->countries as $key => $country) {
            $this->output->writeln('');
            $kw = [];
            foreach($this->keywords[$country->code] as $obj) {
                $kw[] = $obj['keyword_ru'];
            }
            
            $this->output->writeln('<info>Ключевые слова RU-' . $country->code . '</info>: ' . implode(',', $kw));  
            
            $kw = [];
            foreach($this->keywords[$country->code] as $obj) {
                $kw[] = $obj['keyword_en'];
            }
            
            $this->output->writeln('<info>Keywords EN-' . $country->code . '</info>: ' . implode(',', $kw));  
        }        
        
    }
    
    public function prepareKeyword($country, $region) 
    {        
        $this->keywords[$country->code][] = [
            'region_id' => $region->id,
            'keyword_ru' => $this->deleteStopWord($country, $region['name_ru']),  
            'keyword_en' => $this->deleteStopWord($country, $region['name_en']),
        ];

    }
    
    public function deleteStopWord($country, $string) 
    { 
        // Нижний регистр:
        $string = mb_strtolower($string); 
        
        // Удаляю стоп-слова:
        foreach($this->stopWords[$country->code] as $stopWord) {
            $string = str_replace($stopWord, '', $string);
        }
        
        // Склоняю:
        // $string = \morphos\Russian\GeographicalNamesInflection::getCase($string, 'именительный');
        
        // Удаляю пробелы:
        return trim($string);

    }
    
    public function matchCities() 
    {
        // TODO: пройтись по всем городам в каждом стране
        //       искать в описании города ключевые слова, при совпадении установить ид региона
        foreach($this->countries as $key => $country) {
            $this->regionMatches[$country->code]['not_found'] = 0;
            $this->regionMatches[$country->code]['found_1'] = 0;
            $this->regionMatches[$country->code]['found_2'] = 0;
            $this->regionMatches[$country->code]['found_3'] = 0;
            $this->regionMatches[$country->code]['found_4'] = 0;
        }
        
        foreach($this->countries as $key => $country) {
            foreach($country->cities as $key2 => $city) {
                if($country->code == 'RU') {
                    $this->findRegionForCityRU($city);
                } else if ($country->code == 'US') {
                    $this->findRegionForCityUS($city);
                } else {
                    
                }
            }
        }
        
        
        foreach($this->countries as $key => $country) {
            $this->output->writeln(''); 
            $this->output->writeln(
                '<info>Поиск регионов ' . $country->code . ' (точное совпадение)</info>: ' 
                .  $this->regionMatches[$country->code]['found_1'] . ' (' 
                . ceil($this->regionMatches[$country->code]['found_1'] / count($country->cities) * 100).'%)'
            );  
            $this->output->writeln(
                '<info>Поиск регионов ' . $country->code 
                . ' (два совпадения)</info>: ' 
                .  $this->regionMatches[$country->code]['found_2']
            );
            
            $this->output->writeln(
                '<info>Поиск регионов ' . $country->code . ' (>2 совпадений)</info>: ' 
                .  $this->regionMatches[$country->code]['found_3']
            ); 
            
            $this->output->writeln(
                '<info>Не найдено регионов ' . $country->code . '</info>: ' 
                .  $this->regionMatches[$country->code]['not_found']
            );  
        }

        /*
        $this->output->writeln(''); 
        
        $this->output->writeln('<info>Поиск регионов RU (точное совпадение)</info>: ' .  $this->regionMatches['RU']['found_1']);  
        $this->output->writeln('<info>Поиск регионов RU (два совпадения)</info>: ' .  $this->regionMatches['RU']['found_2']);  
        $this->output->writeln('<info>Поиск регионов RU (>2 совпадений)</info>: ' .  $this->regionMatches['RU']['found_3']);  
        $this->output->writeln('<info>Не найдено регионов RU</info>: ' .  $this->regionMatches['RU']['not_found']);  
        
        //$this->output->writeln('RU regions: ' . json_encode($this->regionMatches)); 
        $this->output->writeln(''); */
        
    }

    public function findRegionForCityRU($city)
    {
        
        $this->checkCityCount += 1;
        

        $matchesCount = 0;
        $regionId = 0;
        
        foreach($this->keywords['RU'] as $obj) {
            
            $keywordsCases = GeographicalNamesInflection::getCases($obj['keyword_ru']);

            foreach($keywordsCases as $cases => $keyword) {
                $contains = Str::contains(mb_strtolower($city->description_ru), mb_strtolower($keyword));
                if($contains) {
                    $matchesCount++;
                    $regionId = $obj['region_id'];
                    break;
                }
                
            }

        }
        
        if($matchesCount == 1) {
            if(isset($this->regionMatches['RU']['found_1'])) {
                $this->regionMatches['RU']['found_1'] += 1;
            } else {
                $this->regionMatches['RU']['found_1'] = 1;
            }
            
            $city = City::find($city->id);
            $city->region_id = $regionId;
            $city->save();
            
        } else if($matchesCount == 2) {
            if(isset($this->regionMatches['RU']['found_2'])) {
                $this->regionMatches['RU']['found_2'] += 1;
            } else {
                $this->regionMatches['RU']['found_2'] = 1;
            }
        } else if($matchesCount > 2) {
            if(isset($this->regionMatches['RU']['found_3'])) {
                $this->regionMatches['RU']['found_3'] += 1;
            } else {
                $this->regionMatches['RU']['found_3'] = 1;
            }
        } else {
            if(isset($this->regionMatches['RU']['not_found'])) {
                $this->regionMatches['RU']['not_found'] += 1;
            } else {
                $this->regionMatches['RU']['not_found'] = 1;
            } 
        }
    }
    
    public function findRegionForCityUS($city) 
    {
        $this->checkCityCount += 1;

        $matchesCount = 0;
        $regionId = 0;
        
        foreach($this->keywords['US'] as $obj) {
            $contains = Str::contains(mb_strtolower($city->description), $obj['keyword_en']);
            if($contains) {
                $matchesCount++;
                $regionId = $obj['region_id'];
            }
        }
        
        if($matchesCount == 1) {
            if(isset($this->regionMatches['US']['found_1'])) {
                $this->regionMatches['US']['found_1'] += 1;
            } else {
                $this->regionMatches['US']['found_1'] = 1;
            }
            
            $city = City::find($city->id);
            $city->region_id = $regionId;
            $city->save();
            
        } else if($matchesCount == 2) {
            if(isset($this->regionMatches['US']['found_2'])) {
                $this->regionMatches['US']['found_2'] += 1;
            } else {
                $this->regionMatches['US']['found_2'] = 1;
            }
        } else if($matchesCount > 2) {
            if(isset($this->regionMatches['US']['found_3'])) {
                $this->regionMatches['US']['found_3'] += 1;
            } else {
                $this->regionMatches['US']['found_3'] = 1;
            }
        } else {
            if(isset($this->regionMatches['US']['not_found'])) {
                $this->regionMatches['US']['not_found'] += 1;
            } else {
                $this->regionMatches['US']['not_found'] = 1;
            } 
        }
    }
    
    public function testCases() 
    {
        $string = 'город в Кемеровской области России';
        $keyword = 'кемеровская';
        
        $array = explode(' ', $string);
        foreach($array as $key => $word) {
            // $array[$key] = mb_strtolower($word);
           // $array[$key] = GeographicalNamesInflection::getCases(mb_strtolower($word))['nominative'];
        }
        
        $string = implode($array, ' ');

        
        $this->output->writeln('<info>Строка:</info>: ' . $string);  
        $this->output->writeln('<info>Ключ:</info>: ' . $keyword);  
        
        $contains = Str::contains($string, $keyword);
        if($contains) {
            $this->output->writeln('<info>Найдено!</info>');  
        } else {
            $this->output->writeln('<info>Не найдено :(</info>');  
        }
        
        $a = GeographicalNamesInflection::getCases($keyword);
        
        foreach($a as $case => $kw) {
            $this->output->writeln($case . ': ' . mb_strtolower($kw));
        }

            


        
        
    }
    
}
