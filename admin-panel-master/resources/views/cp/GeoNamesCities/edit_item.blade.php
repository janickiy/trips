@extends('cp.cp_tk')

@section('content_title') Просмотр города из GeoNames #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/geonames_cities" id="items_list">Города из GeoNames</a></li>
    <li class="active">Просмотр города из GeoNames #{{$item->id}}:</li>
</ol>
@endsection

@section('content')

@if (Session::has('item_created'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ Session::get('item_created') }}</h4>
            @if (Session::has('details'))
            <p>{{ Session::get('details') }}</p>
            @endif
        </div>
    </div>
</div>
@endif

@if (Session::has('update_item'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ Session::get('update_item') }}</h4>
            @if (Session::has('details'))
            <p>{{ Session::get('details') }}</p>
            @endif
        </div>
    </div>
</div>
@endif

<form method="post" action="{{URL::to('/')}}/cp/wikidata_country/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">

<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">На русском</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="name_ru" value="{{$item->name_ru}}" placeholder="{{__('wikidata_country.name_ru')}}" class="form-control input-sm" id="column_name_ru"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">На английском</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="name_en" value="{{$item->name}}" placeholder="{{__('wikidata_country.name')}}" class="form-control input-sm" id="column_name"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">GeoNames ID</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="geonameid" value="{{$item->geonameid}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
</div>

</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">latitude</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="latitude" value="{{$item->latitude}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
</div>

</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">longitude</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="longitude" value="{{$item->longitude}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
</div>

</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">feature_class</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="feature_class" value="{{$item->feature_class}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
<div class="form-group">
    {{$featureClasses[$item->feature_class]}}
</div>
</div>

</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">feature_code</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="feature_code" value="{{$item->feature_code}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
<div class="form-group">
    {{$featureCodes[$item->feature_code]}}
</div>
</div>

</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">population</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">
    <input type="text" name="population" value="{{$item->population}}" class="form-control input-sm" id="column_name_ru"  disabled>
</div>
</div>

</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">country_code</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="country_code" value="{{$item->country_code}}" placeholder="{{__('wikidata_country.country_code')}}" class="form-control input-sm" id="column_country_code"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Обновлен</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="modification_date" value="{{$item->modification_date}}" placeholder="{{__('wikidata_country.modification_date')}}" class="form-control input-sm" id="column_modification_date"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->

    </div>

    <div class="col-md-4">

    </div>

</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<!--
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('wikidata_country.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_country.save') }}">
            </div>
        </div>
    </div>
</div>
S -->
<!-- /ACTION BUTTONS -->


<!-- Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body">
      <h5 class="modal-title" id="delete_confirmation">{{ __('wikidata_country.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('wikidata_country.delete') }}">
      </div>
    </div>
  </div>
</div>

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

key('del', function(event, handler){
    $('#delete_modal').modal();
});

// TODO: create by button
</script>
@endsection
