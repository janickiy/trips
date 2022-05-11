# Минимальные требования к серверу:
  - VPS
  - PHP >=7.1
  - MySQL >= 5.6

# Фронтенд:

  - [AdminLTE v2.4.15](https://adminlte.io/themes/AdminLTE/index2.html)
  - [Bootstrap v3.4.1](https://getbootstrap.com/docs/3.4)
  - [Font Awesome 4.7.0](https://fontawesome.com/v4.7.0)
  - jQuery v3.3.1

# Бекенд:

  - [Laravel framework 5.8](https://laravel.com/docs/5.8/configuration)
  - [League CSV 9.0](https://csv.thephpleague.com/9.0)
  - [Morphos 3.2](https://github.com/wapmorgan/Morphos/blob/master/README-ru.md)
  - [Wikidata 3.5](https://github.com/freearhey/wikidata)
  - [SPARQL Asparagus 0.4.2](https://github.com/Benestar/asparagus)
  - [JPEGoptim 1.4.1](http://freshmeat.sourceforge.net/projects/jpegoptim)

# Описание базы данных:

Проект содержит в себе три базы данных из открытых источников и объединенной базы данных, которая будет использоваться в приложении. 

| Раздел | Таблицы |
| ------ | ------ |
| [WikiData](https://www.wikidata.org/wiki/Wikidata:Main_Page) | wikidata_cities, wikidata_countries, wikidata_regions |
| [GeoNames](https://www.geonames.org) | geonames_cities, geonames_countries |
| [TravelPayouts](https://www.travelpayouts.com/ru/) | travelpayout_cities, travelpayout_countries |
| Объединенные данные |  cities_1000, countries, regions |