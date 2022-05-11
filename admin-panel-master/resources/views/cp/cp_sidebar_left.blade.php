<!-- Страницы меню: -->
<ul class="sidebar-menu" data-widget="tree">


<div class="crm-sidebar-header">
    <p>CRM</p>
</div>


<li class="">
    <a href="{{URL::to('/')}}/cp/dashboard">
        <i class="fa fa-dashboard"></i>
        <span>Дэшборд</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/trips_user">
        <i class="fa fa-users"></i>
        <span>Пользователи Trips</span>
        <span class="pull-right-container"></span>
    </a>
</li>


<div class="crm-sidebar-header">
    <p>База городов</p>
</div>


<li class="header">Объединенные данные:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/country">
        <i class="fa fa-map"></i>
        <span>Страны</span>
        <span class="pull-right-container"></span>
    </a>
</li>


<li class="">
    <a href="{{URL::to('/')}}/cp/region">
        <i class="fa fa-map-signs"></i>
        <span>Регионы</span>
        <span class="pull-right-container"></span>
    </a>
</li>


<li class="">
    <a href="{{URL::to('/')}}/cp/cities">
        <i class="fa fa-university"></i>
        <span>Города</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/cities-map">
        <i class="fa fa-map-o"></i>
        <span>Карта городов</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="header">На модерации:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/cities_on_moderation">
        <i class="fa fa-university"></i>
        <span>Города без Wiki-entity</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/duplicates">
        <i class="fa fa-university"></i>
        <span>Города дубликаты</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/city_photos">
        <i class="fa fa-image"></i>
        <span>Фото городов</span>
        <span class="pull-right-container"></span>
    </a>
</li>


<li class="header">Исходные данные:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/wikidata_countries">
        <i class="fa fa-map"></i>
        <span>Страны из WikiData</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/travelpayouts_countries">
        <i class="fa fa-map"></i>
        <span>Страны из Travelpayouts</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<!--
<li class="">
    <a href="{{URL::to('/')}}/cp/wikidata_regions">
        <i class="fa fa-map-signs"></i>
        <span>Регионы из WikiData</span>
        <span class="pull-right-container"></span>
    </a>
</li>
-->
<li class="">
    <a href="{{URL::to('/')}}/cp/geonames_cities">
        <i class="fa fa-university"></i>
        <span>Города из GeoNames</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/wikidata_cities">
        <i class="fa fa-university"></i>
        <span>Города из WikiData</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/travelpayouts">
        <i class="fa fa-university"></i>
        <span>Города из Travelpayouts</span>
        <span class="pull-right-container"></span>
    </a>
</li>



<!--
<li class="header">Запросы к WikiData:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/prepared_queries">
        <i class="fa fa-th"></i>
        <span>Тестовые запросы</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/city_properties">
        <i class="fa fa-university"></i>
        <span>Свойства города</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/instanse_of">
        <i class="fa fa-cube"></i>
        <span>Чем является</span>
        <span class="pull-right-container"></span>
    </a>
</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/get_country">
        <i class="fa fa-flag"></i>
        <span>Какой стране принадлежит</span>
        <span class="pull-right-container"></span>
    </a>
</li>
-->

<!--
<li class="header">Парсинг:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/download_countries">
        <i class="fa fa-download"></i>
        <span>WikiData: Загрузка стран</span>
        <span class="pull-right-container"></span>
    </a>
</li>


<li class="">
    <a href="{{URL::to('/')}}/cp/geodata_cities_wikidata">
        <i class="fa fa-download"></i>
        <span>GeoData: Парсинг городов Wiki</span>
        <span class="pull-right-container"></span>
    </a>
</li>
-->

<!--
<li class="header">Логи:</li>

<li class="">
    <a href="{{URL::to('/')}}/cp/wikidata_city_parser_log">
        <i class="fa fa-file-text-o"></i>
        <span>GeoData: Логи парсинга Wiki</span>
        <span class="pull-right-container"></span>
    </a>
</li>
-->






<li class="header">Остальное:</li>


<div class="crm-sidebar-header">
    <p>Настройки админки</p>
</div>

<li class="">
    <a href="{{URL::to('/')}}/cp/user">
        <i class="fa fa-users"></i>
        <span>Пользователи админки</span>
        <span class="pull-right-container"></span>
    </a>
</li>


</ul>
