@extends('cp.cp_tk')

@section('content_title') Объединенные города @endsection

@section('content_breadcrumb')



<div style="display: flex; justify-content: space-between;">
<div style="display: flex;">
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Объединенные города:</li>
</ol>
</div>
<div style="   ">
    <a href="{{URL::to('/')}}/cp/download_sql" 
    target="_blank" 
    title="Будет загружена полностью вся база городов в формате SQL" 
    class="btn btn-block btn-info btn-sm">
        <i class="fa fa-fw fa-database"></i> Скачать SQL
    </a>
</div>

</div>

@endsection

@section('content_description')
    Это конечная база данных городов, которую можно использовать в приложении.
@endsection


@section('content')

@if (Session::has('deleted_item'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ Session::get('deleted_item') }}</h4>
            @if (Session::has('details'))
            <p>{{ Session::get('details') }}</p>
            @endif
        </div>
    </div>
</div>
@endif


<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
    <!---- ADD NEW BUTTON: ---->
        <p>
            <a href="{{URL::to('/')}}/cp/cities/add" class="btn btn-success" id="add_new">
                {{ __('cities_1000.create_new') }}
            </a> 
        </p>
    <!---- /ADD NEW BUTTON ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_cities_1000">
        <div class="input-group">
            <input placeholder="Поиск городов" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <input type="hidden" name="population_sort" value="desc">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/cities" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    </form>
    <!---- /SEARCH FIELD ---->

    </div>
</div>




<!---- CONTENT: ---->
<div class="row">
    <div class="col-xs-12">
        <div class="box">

<div class="box-body table-responsive no-padding">
    <table class="table table-striped tr-middle">
        <thead>
<tr><th>{{__('cities_1000.id')}}</th>
<th>{{__('cities_1000.name')}}</th>
<th>{{__('cities_1000.name_ru')}}</th>
<th class="text-center"><i class="fa fa-wikipedia-w"></i></th>
<th>{{__('cities_1000.country_code')}}</th>
<th>IATA</th>
<th>Страна / Регион</th>
<th>{{__('cities_1000.longitude')}}</th>
<th>{{__('cities_1000.latitude')}}</th>
<th><i class="fa fa-map-o"></i></th>
<th>

{{__('cities_1000.population')}}
@if(app('request')->has('population_sort'))
    @if(app('request')->input('population_sort') == 'asc')
        <a href="{{URL::to('/')}}/cp/cities?q={{app('request')->input('q')}}&population_sort=desc"><i class="fa fa-fw fa-sort-amount-asc" title="По возрастанию"></i></a>
    @elseif (app('request')->input('population_sort') == 'desc')
        <a href="{{URL::to('/')}}/cp/cities?q={{app('request')->input('q')}}&population_sort=asc"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
    @endif
@else 
    <a href="{{URL::to('/')}}/cp/cities?q={{app('request')->input('q')}}&population_sort=asc" style="color: #000;"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
@endif

</th>
<th>Падежи</th>
<th>Модерация</th>
<th>Обновлен</th>
<th><i class="fa fa-fw fa-pencil" title="Город был отредактирован вручную"></i></th>
<th><i class="fa fa-fw fa-edit"></i> {{__('cities_1000.edit')}}</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
<tr>
    
    <td>{{$item->id}}</td>
    <td>{{$item->name}}</td>
    <td>{{$item->name_ru}}</td>    
    <td>@if($item->wiki_entity != null)<a href="https://www.wikidata.org/wiki/{{$item->wiki_entity}}" target="_blank">{{$item->wiki_entity}}</a>@endif</td>
    <td>{{$item->country_code}}</td>
    <td>{{$item->iata_code}}</td>
    <td>
        <a href="{{URL::to('/')}}/cp/country/edit/{{$item->country['id']}}">{{$item->country['name_ru']}}</a>
        @if($item->region_id > 0) / {{$item->countryRegion['name_ru']}} @endif
    </td>
    @php $lat = '@' .$item->latitude; @endphp 
    <td>{{$item->longitude}}</td>
    <td>{{$item->latitude}}</td>
    <td>
        <a href="https://www.google.com/maps/{{$lat}},{{$item->longitude}},3000m/data=!3m1!1e3?hl=ru" target="_blank"><i class="fa fa-fw fa-map-marker"></i></a>
    </td>
    <td>{{$item->population}}</td>
    <td class="text-center">
        @if($item->genitive != null)<i class="fa fa-circle text-success"></i>@endif
    </td>
    <td>
        @if($item->deleted_at != null)
            <span class="badge bg-red">Удалено</span>
        @endif
    </td>
    <td>
        @if($item->deleted_at != null)
            {{$item->deleted_at}}
        @else 
            {{$item->updated_at}}
        @endif
    </td>
    <td>
        @if($item->custom_edited == 1)
            <i class="fa fa-fw fa-pencil" title="Город был отредактирован вручную"></i>
        @endif
    </td>


    <td>
        <a href="{{URL::to("/")}}/cp/cities/edit/{{$item->id}}">
        <i class="fa fa-fw fa-edit"></i> {{__('cities_1000.edit')}}
        </a>
    </td>
</tr>
@empty
    <tr><td colspan="42">{{__('cities_1000.no_records')}}</td></tr>
@endforelse

        </tbody>
        
    </table>
</div>

        </div>
    </div>
</div>
<!---- /CONTENT ---->



<div class="row">
    <div class="col-xs-6">
    
<div class="card-footer d-block">
    <div class="text-left">{{$items->links("cp.cp_pagination")}}</div>
</div>

<div class="card-footer d-block">
    <div class="text-left">Всего найдено записей: {{$itemsCount}} (<a href="{{URL::to('/')}}/api/download_cities_1000_csv?q={{ app('request')->input('q') }}&population_sort={{ app('request')->input('population_sort') }}">Скачать .CSV</a>)
    <br>Удаленные записи не будут экспортированы в .csv.
    </div>
</div>


    
    </div>
    
    <div class="col-xs-6">

<div class="box box-success" style="position: relative; left: 0px; top: 0px;">

    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Загрузить данные из .СSV:</h3>
    </div>

    <div class="box-body">

<form method="post" autocomplete="off" action="{{URL::to('/')}}/cp/upload_cities_csv" enctype="multipart/form-data">
{{ csrf_field() }}

    <div class="card-footer d-block m-2">
        

            <input type="file" name="csv_file" accept=".csv, .txt">        
            <input type="hidden" name="test" value="1">        
        
    </div>
    <div class="card-footer d-block">
        <div class="text-left"><br><input type="submit" class="btn btn-success" name="submit" value="Загрузить"></div>
    </div>
    
</form>
    
    </div>
    <div class="box-footer">
    В .CSV обязательно должен быть указан ID города из нашей базы данных. 
    </div>
    
</div>
    
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">

    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Справка:</h3>
    </div>

    <div class="box-body">
        <div class="form-group">
            <i class="fa fa-circle text-success"></i> - указывает, что для русского названия есть падежи<br>
            <i class="fa fa-fw fa-pencil"></i> - указывает, что город был отредактирован вручную, поле custom_edited
            
        </div>
    </div>
    
</div>

    </div>
</div>







@endsection

@section('scripts')
<script>

var oneKey = false;

function redirect(id) {
    if ($(id).length) {
        if(!oneKey) {
            document.location.href = $(id).attr('href');
            oneKey = true;
        }
    }
}

key('right', function(event, handler){
    redirect('#next_page');
});

key('left', function(event, handler){
    redirect('#prev_page');
});

key('n', function(event, handler){
    redirect('#add_new');
});

</script>
@endsection