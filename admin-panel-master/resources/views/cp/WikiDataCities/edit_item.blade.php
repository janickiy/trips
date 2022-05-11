@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_city.edit_wikidata_city') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_cities" id="items_list">{{ __('wikidata_city.wikidata_city') }}</a></li>
    <li class="active">{{ __('wikidata_city.edit_wikidata_city') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/wikidata_city/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">

<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->


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

<input type="text" name="wiki_entity" value="{{$item->wiki_entity}}" placeholder="{{__('wikidata_city.wiki_entity')}}" class="form-control input-sm" id="column_wiki_entity"  disabled>

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

<input type="text" name="geonameid" value="{{$item->geonameid}}" placeholder="{{__('wikidata_city.geonameid')}}" class="form-control input-sm" id="column_geonameid"  disabled>

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

<input type="text" name="name" value="{{$item->name}}" placeholder="{{__('wikidata_city.name')}}" class="form-control input-sm" id="column_name"  disabled>

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

<input type="text" name="description" value="{{$item->description}}" placeholder="{{__('wikidata_city.description')}}" class="form-control input-sm" id="column_description"  disabled>

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

<input type="text" name="name_ru" value="{{$item->name_ru}}" placeholder="{{__('wikidata_city.name_ru')}}" class="form-control input-sm" id="column_name_ru"  disabled>

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

<input type="text" name="description_ru" value="{{$item->description_ru}}" placeholder="{{__('wikidata_city.description_ru')}}" class="form-control input-sm" id="column_description_ru"  disabled>

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

<input type="text" name="latitude" value="{{$item->latitude}}" placeholder="{{__('wikidata_city.latitude')}}" class="form-control input-sm" id="column_latitude"  disabled>

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

<input type="text" name="longitude" value="{{$item->longitude}}" placeholder="{{__('wikidata_city.longitude')}}" class="form-control input-sm" id="column_longitude"  disabled>

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

<input type="text" name="country_code" value="{{$item->country_code}}" placeholder="{{__('wikidata_city.country_code')}}" class="form-control input-sm" id="column_country_code"  disabled>

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

<input type="text" name="iata_code" value="{{$item->iata_code}}" placeholder="{{__('wikidata_city.iata_code')}}" class="form-control input-sm" id="column_iata_code"  disabled>

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

<input type="number" name="population" value="{{$item->population}}" placeholder="{{__('wikidata_city.population')}}" id="column_population" class="form-control"  disabled>

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

<input type="text" name="modification_date" value="{{$item->modification_date}}" placeholder="{{__('wikidata_city.modification_date')}}" class="form-control input-sm" id="column_modification_date"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_city  ---->




    </div>

    <div class="col-md-4">

        <!----   wikidata_city  ---->
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
        <div class="box-header ui-sortable-handle">

            <!--- Essense:  --->
            <h3 class="box-title">TODO:</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

        </div>

        <div class="box-body">
        <div class="form-group">
UPDATE
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

        <input type="hidden" name="region" value="{{$item->region}}" id="column_region">

                                        </div>
        </div>


        </div>
        <!----   /wikidata_city  ---->


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
                    {{ __('wikidata_city.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_city.save') }}">
            </div>
        </div>
    </div>
</div>
 -->
<!-- /ACTION BUTTONS -->


<!-- Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body">
      <h5 class="modal-title" id="delete_confirmation">{{ __('wikidata_city.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('wikidata_city.delete') }}">
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
