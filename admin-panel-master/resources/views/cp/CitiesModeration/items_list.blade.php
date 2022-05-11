@extends('cp.cp_tk')

@section('content_title') Города без Wiki-entity @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Города без Wiki-entity:</li>
</ol>
@endsection

@section('content_description')
Здесь отображаются города из объединенной базы данных, у которых не указано Q-entity.
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
<span class="input-group-addon">
  <label style="margin-bottom: 0px; margin-right: 10px;">
    <input type="checkbox" name="iata" style="float: left; margin-right: 3px;" @if(app('request')->has('iata') and app('request')->input('iata') == 'on') checked @endif> 
    <div style="margin-top: 2px; ">IATA</div>
  </label>
</span>
            <input placeholder="{{ __('cities_1000.search_cities_1000') }}" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/cities_on_moderation" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    </form>
    <!---- /SEARCH FIELD ---->

    </div>
</div>
<style>
.input-group-addon input[type=checkbox], .input-group-addon input[type=radio] {
     margin-top: 2px !important;
}
</style>



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
<th>GeoNames Class:</th>
<th>GeoNames Code:</th>
<th>{{__('cities_1000.longitude')}}</th>
<th>{{__('cities_1000.latitude')}}</th>
<th><i class="fa fa-map-o"></i></th>
<th>

{{__('cities_1000.population')}}
@if(app('request')->has('population_sort'))
    @if(app('request')->input('population_sort') == 'asc')
        <a href="{{URL::to('/')}}/cp/cities_on_moderation?q={{app('request')->input('q')}}&iata={{app('request')->input('iata')}}&population_sort=desc"><i class="fa fa-fw fa-sort-amount-asc" title="По возрастанию"></i></a>
    @elseif (app('request')->input('population_sort') == 'desc')
        <a href="{{URL::to('/')}}/cp/cities_on_moderation?q={{app('request')->input('q')}}&iata={{app('request')->input('iata')}}&population_sort=asc"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
    @endif
@else 
    <a href="{{URL::to('/')}}/cp/cities_on_moderation?q={{app('request')->input('q')}}&iata={{app('request')->input('iata')}}&population_sort=asc" style="color: #000;"><i class="fa fa-fw fa-sort-amount-desc" title="По убыванию"></i></a>
@endif

</th>
<th>Падежи</th>
<th>Модерация</th>
<th>Обновлен</th>
<th><i class="fa fa-fw fa-star-o" title="Город был отредактирован вручную"></i></th>
<th><i class="fa fa-fw fa-edit"></i> {{__('cities_1000.edit')}}</th>
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
<td><a href="{{URL::to('/')}}/cp/country/edit/{{$item->country['id']}}">{{$item->country['name_ru']}}</a></td>
<td>{{$item->feature_class}}</td>
<td>{{$item->feature_code}}</td>
@php $lat = '@' .$item->latitude; @endphp 
<td>{{$item->longitude}}</td>
<td>{{$item->latitude}}</td>
<td><a href="https://www.google.com/maps/{{$lat}},{{$item->longitude}},3000m/data=!3m1!1e3?hl=ru" target="_blank"><i class="fa fa-fw fa-map-marker"></i></a></td>
<td>{{$item->population}}</td>
<td class="text-center">@if($item->genitive != null)<i class="fa fa-circle text-success"></i>@endif</td>
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