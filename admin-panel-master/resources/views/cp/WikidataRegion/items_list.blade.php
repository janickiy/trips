@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_region.wikidata_region') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">{{ __('wikidata_region.wikidata_region') }}:</li>
</ol>
@endsection


@section('content_description')
Тут будут регионы, когда узнаем какие именно и как скачать. 
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
    <!---- 
        <p>
            <a href="{{URL::to('/')}}/cp/wikidata_region/add" class="btn btn-success" id="add_new">
                {{ __('wikidata_region.create_new') }}
            </a> 
        </p>
     ---->
    <!---- /ADD NEW BUTTON ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_wikidata_region">
    <p>
        <div class="input-group">
            <input placeholder="{{ __('wikidata_region.search_wikidata_region') }}" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/wikidata_regions" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    <p>
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
<tr><th>{{__('wikidata_region.id')}}</th>
<th>{{__('wikidata_region.county_id')}}</th>
<th class="text-center"><i class="fa fa-wikipedia-w"></i></th>
<th>{{__('wikidata_region.name_en')}}</th>
<th>{{__('wikidata_region.name_ru')}}</th>
<th><i class="fa fa-fw fa-edit"></i> {{__('wikidata_region.edit')}}</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td>{{$item->county_id}}</td>
<td class="text-center">{{$item->wiki_entity}}</td>
<td>{{$item->name_en}}</td>
<td>{{$item->name_ru}}</td>
<td>
    <a href="{{URL::to("/")}}/cp/wikidata_region/edit/{{$item->id}}">
    <i class="fa fa-fw fa-edit"></i> {{__('wikidata_region.edit')}}
    </a></td>
</tr>
@empty
    <tr><td colspan="42">{{__('wikidata_region.no_records')}}</td></tr>
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