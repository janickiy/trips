@extends('cp.cp_tk')

@section('content_title') Города из WikiData @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Города из WikiData:</li>
</ol>
@endsection


@section('content_description')
Здесь отображаются города, скачанные из WikiData. Только просмотр, нет редактирования. 
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
    <!----
        <p>
            <a href="{{URL::to('/')}}/cp/cities_1000/add" class="btn btn-success" id="add_new">
                {{ __('cities_1000.create_new') }}
            </a>
        </p>
     ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_cities_1000">
        <p>
        <div class="input-group">
            <input placeholder="Поиск городов из WikiData" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/wikidata_cities" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    </p>
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
<th>Страна</th>
<th>{{__('cities_1000.longitude')}}</th>
<th>{{__('cities_1000.latitude')}}</th>
<th><i class="fa fa-map-o"></i></th>
<th>

{{__('cities_1000.population')}}
@if(app('request')->has('population_sort'))
    @if(app('request')->input('population_sort') == 'asc')
        <a href="{{URL::to('/')}}/cp/wikidata_cities?q={{app('request')->input('q')}}&population_sort=desc"><i class="fa fa-fw fa-sort-amount-asc" title="По возрастанию"></i></a>
    @elseif (app('request')->input('population_sort') == 'desc')
        <a href="{{URL::to('/')}}/cp/wikidata_cities?q={{app('request')->input('q')}}&population_sort=asc"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
    @endif
@else
    <a href="{{URL::to('/')}}/cp/wikidata_cities?q={{app('request')->input('q')}}&population_sort=asc" style="color: #000;"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
@endif

</th>
<th>Обновлен</th>
<th><i class="fa fa-fw fa-eye"></i></th>
<tr>

        </thead>

        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td>{{$item->name}}</td>
<td>{{$item->name_ru}}</td>
<td>@if($item->wiki_entity != null)<a href="https://www.wikidata.org/wiki/{{$item->wiki_entity}}" target="_blank">{{$item->wiki_entity}}</a>@endif</td>
<td>{{$item->country_code}}</td>
<td>{{$item->iata_code}}</td>
<td>@if($item->country != null)<a href="{{URL::to('/')}}/cp/wikidata_countries/edit/{{$item->country['id']}}">{{$item->country['name_ru']}}</a>@endif</td>
@php $lat = '@' .$item->latitude; @endphp
<td>{{$item->longitude}}</td>
<td>{{$item->latitude}}</td>
<td><a href="https://www.google.com/maps/{{$lat}},{{$item->longitude}},3000m/data=!3m1!1e3?hl=ru" target="_blank"><i class="fa fa-fw fa-map-marker"></i></a></td>
<td>{{$item->population}}</td>
<td>{{$item->modification_date}}</td>

<td>
    <a href="{{URL::to("/")}}/cp/wikidata_cities/edit/{{$item->id}}">
    <i class="fa fa-fw fa-eye"></i>
    </a></td>
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




<div class="card-footer d-block">
    <div class="text-left">Всего найдено записей: {{$itemsCount}} <!--(<a href="{{URL::to('/')}}/api/download_cities_1000_csv?q={{ app('request')->input('q') }}&population_sort={{ app('request')->input('population_sort') }}">Скачать .CSV</a>)--></div>
</div>
<div class="card-footer d-block">
    <div class="text-left">{{$items->links("cp.cp_pagination")}}</div>
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
