@extends('cp.cp_tk')

@section('content_title') {{ __('cities_1000.create_cities_1000') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/cities" id="items_list">Объединенные города</a></li>
    <li class="active">{{ __('cities_1000.create_cities_1000') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/cities/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}



<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.name') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name" value="" placeholder="{{__('cities_1000.name')}}" class="form-control input-sm" id="column_name" required >
</div>
</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.name_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name_ru" value="" placeholder="{{__('cities_1000.name_ru')}}" class="form-control input-sm" id="column_name_ru">
</div>
</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.asciiname') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="asciiname" value="" placeholder="{{__('cities_1000.asciiname')}}" class="form-control input-sm" id="column_asciiname"  >
</div>
</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.alternatenames') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<textarea name="alternatenames" id="column_alternatenames"></textarea>
<script>CKEDITOR.replace( "column_alternatenames" );</script>
</div>
</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.latitude') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="latitude" value="" placeholder="{{__('cities_1000.latitude')}}" class="form-control" id="column_latitude"  >
</div>
</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.longitude') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="longitude" value="" placeholder="{{__('cities_1000.longitude')}}" class="form-control" id="column_longitude"  >
</div>
</div>


</div>
<!----   /cities_1000  ---->






<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.population') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="number" name="population" value="0" placeholder="{{__('cities_1000.population')}}" id="column_population" class="form-control"  >
</div>
</div>


</div>
<!----   /cities_1000  ---->





    </div>
    
    <div class="col-md-4">
        
        
<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.country_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="country_code" value="" placeholder="{{__('cities_1000.country_code')}}" class="form-control input-sm" id="column_country_code"  >
</div>
</div>


</div>
<!----   /cities_1000  ----> 
     

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.wiki_entity') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="wiki_entity" value="" placeholder="{{__('cities_1000.wiki_entity')}}" class="form-control input-sm" id="column_wiki_entity">
</div>
</div>

<div class="box-footer">
Пример: Q64
</div>


</div>
<!----   /cities_1000  ----> 
        
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('cities_1000.create') }}">
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