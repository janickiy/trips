@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_city.create_wikidata_city') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_city" id="items_list">{{ __('wikidata_city.wikidata_city') }}</a></li>
    <li class="active">{{ __('wikidata_city.create_wikidata_city') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/wikidata_city/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}



<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.wiki_entity') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="wiki_entity" value="" placeholder="{{__('wikidata_city.wiki_entity')}}" class="form-control input-sm" id="column_wiki_entity"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.geonameid') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="geonameid" value="" placeholder="{{__('wikidata_city.geonameid')}}" class="form-control input-sm" id="column_geonameid"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.name') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name" value="" placeholder="{{__('wikidata_city.name')}}" class="form-control input-sm" id="column_name"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.description') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="description" value="" placeholder="{{__('wikidata_city.description')}}" class="" id="column_description"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.genitive') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="genitive" value="" id="column_genitive">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.dative') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="dative" value="" id="column_dative">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.accusative') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="accusative" value="" id="column_accusative">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.instrumental') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="instrumental" value="" id="column_instrumental">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.prepositional') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="prepositional" value="" id="column_prepositional">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.without_diacritics') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="without_diacritics" value="" id="column_without_diacritics">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.name_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name_ru" value="" placeholder="{{__('wikidata_city.name_ru')}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.description_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="description_ru" value="" placeholder="{{__('wikidata_city.description_ru')}}" class="" id="column_description_ru"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.asciiname') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="asciiname" value="" id="column_asciiname">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.alternatenames') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="alternatenames" value="" id="column_alternatenames">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.latitude') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="latitude" value="" placeholder="{{__('wikidata_city.latitude')}}" class="" id="column_latitude"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.longitude') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="longitude" value="" placeholder="{{__('wikidata_city.longitude')}}" class="" id="column_longitude"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.feature_class') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="feature_class" value="" id="column_feature_class">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.feature_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="feature_code" value="" id="column_feature_code">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.country_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="country_code" value="" placeholder="{{__('wikidata_city.country_code')}}" class="form-control input-sm" id="column_country_code"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.iata_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="iata_code" value="" placeholder="{{__('wikidata_city.iata_code')}}" class="form-control input-sm" id="column_iata_code"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.cc2') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="cc2" value="" id="column_cc2">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.region') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="region" value="" id="column_region">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.admin1_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="admin1_code" value="" id="column_admin1_code">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.admin2_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="admin2_code" value="" id="column_admin2_code">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.admin3_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="admin3_code" value="" id="column_admin3_code">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.admin4_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="admin4_code" value="" id="column_admin4_code">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.population') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="number" name="population" value="0" placeholder="{{__('wikidata_city.population')}}" id="column_population" class="form-control"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.elevation') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="elevation" value="" id="column_elevation">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.dem') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="dem" value="" id="column_dem">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.timezone') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="timezone" value="" id="column_timezone">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.modification_date') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="modification_date" value="" placeholder="{{__('wikidata_city.modification_date')}}" class="form-control input-sm" id="column_modification_date"  disabled>
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.custom_edited') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="custom_edited" value="" id="column_custom_edited">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

<!----   wikidata_city  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city.need_custom_moderation') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="hidden" name="need_custom_moderation" value="" id="column_need_custom_moderation">
</div>
</div>


</div>
<!----   /wikidata_city  ---->

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
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_city.create') }}">
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