@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_region.create_wikidata_region') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_region" id="items_list">{{ __('wikidata_region.wikidata_region') }}</a></li>
    <li class="active">{{ __('wikidata_region.create_wikidata_region') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/wikidata_region/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}



<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!----   wikidata_region  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_region.county_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="county_id" value="" placeholder="{{__('wikidata_region.county_id')}}" class="form-control input-sm" id="column_county_id"  >
</div>
</div>


</div>
<!----   /wikidata_region  ---->

<!----   wikidata_region  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_region.wiki_entity') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="wiki_entity" value="" placeholder="{{__('wikidata_region.wiki_entity')}}" class="form-control input-sm" id="column_wiki_entity"  >
</div>
</div>


</div>
<!----   /wikidata_region  ---->

<!----   wikidata_region  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_region.name_en') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name_en" value="" placeholder="{{__('wikidata_region.name_en')}}" class="form-control input-sm" id="column_name_en"  >
</div>
</div>


</div>
<!----   /wikidata_region  ---->

<!----   wikidata_region  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_region.name_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name_ru" value="" placeholder="{{__('wikidata_region.name_ru')}}" class="form-control input-sm" id="column_name_ru"  >
</div>
</div>


</div>
<!----   /wikidata_region  ---->

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
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_region.create') }}">
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