@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_city_parser_log.create_wikidata_city_parser_log') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_city_parser_log" id="items_list">{{ __('wikidata_city_parser_log.wikidata_city_parser_log') }}</a></li>
    <li class="active">{{ __('wikidata_city_parser_log.create_wikidata_city_parser_log') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/wikidata_city_parser_log/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}



<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.city_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="number" name="city_id" value="0" placeholder="{{__('wikidata_city_parser_log.city_id')}}" id="column_city_id" class="form-control"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.category_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="category_id" value="" placeholder="{{__('wikidata_city_parser_log.category_id')}}" class="form-control input-sm" id="column_category_id"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.reason_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="reason_id" value="" placeholder="{{__('wikidata_city_parser_log.reason_id')}}" class="form-control input-sm" id="column_reason_id"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.search_results') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="search_results" value="" id="column_search_results">
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.old_population') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="old_population" value="" placeholder="{{__('wikidata_city_parser_log.old_population')}}" class="form-control input-sm" id="column_old_population"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.query_time') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="query_time" value="" placeholder="{{__('wikidata_city_parser_log.query_time')}}" class="" id="column_query_time"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.created_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="created_at" value="" id="column_created_at">
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.updated_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="updated_at" value="" placeholder="{{__('wikidata_city_parser_log.updated_at')}}" class="form-control input-sm" id="column_updated_at"  >
</div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

    </div>
    
    <div class="col-md-4">
        
        
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_city_parser_log.create') }}">
            </div>
        </div>
    </div>
</div>
<!-- /ACTION BUTTONS -->

</form>

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

key('l', function(event, handler){
    redirect('#items_list');
});

// TODO: create by button
</script>
@endsection