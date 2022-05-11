@extends('cp.cp_tk')

@section('content_title') Логи парсинга WikiData @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Логи парсинга WikiData:</li>
</ol>
@endsection

@section('content_description')
Сюда можно не заходить. Здесь отображаются логи парсеров.
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
            <!--<a href="{{URL::to('/')}}/cp/wikidata_city_parser_log/add" class="btn btn-success" id="add_new">
                {{ __('wikidata_city_parser_log.create_new') }}
            </a> -->
            
        </p>
    <!---- /ADD NEW BUTTON ---->
    
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">
 <p>
    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_wikidata_city_parser_log">
        <div class="input-group">
            <input placeholder="Поиск логов по ID города" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/wikidata_city_parser_log" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    </form>
 </p>
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
<tr><th>{{__('wikidata_city_parser_log.id')}}</th>
<th>{{__('wikidata_city_parser_log.city_id')}}</th>
<th>Статус</th>
<th>Лог</th>
<th>{{__('wikidata_city_parser_log.old_population')}}</th>
<th>Дельта</th>
<th>{{__('wikidata_city_parser_log.query_time')}}</th>
<th>{{__('wikidata_city_parser_log.updated_at')}}</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td><a href="{{URL::to('/')}}/cp/cities_1000/edit/{{$item->city_id}}">{{$item->city['name']}}</a></td>
<td>{{$item->category_id}}</td>
<td>{{$errors[$item->category_id][$item->reason_id]}}</td>
<td>{{$item->old_population}}</td>
<td>@if($item->population_change > 0){{""}}+{{""}}@endif{{$item->population_change}}</td>
<td>{{$item->query_time}} сек.</td>
<td>{{$item->updated_at}}</td>
@empty
    <tr><td colspan="42">{{__('wikidata_city_parser_log.no_records')}}</td></tr>
@endforelse

        </tbody>
        
    </table>
</div>

        </div>
    </div>
</div>
<!---- /CONTENT ---->


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