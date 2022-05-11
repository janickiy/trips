@extends('cp.cp_tk')

@section('content_title') {{ __('cities_1000.edit_cities_1000') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/cities_1000" id="items_list">{{ __('cities_1000.cities_1000') }}</a></li>
    <li class="active">{{ __('cities_1000.edit_cities_1000') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/cities/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-6">
        
<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->




<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Название</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">На английском</label></div>
  <div class="col-sm-8"><input type="text" name="name" value="{{$item->name}}" placeholder="{{__('cities_1000.name')}}" class="form-control input-sm" id="column_name" required ></div>
</div>


<div class="form-group">
    <div class="col-sm-4">
        <label for="" class=" control-label">На русском</label>
    </div>
    <div class="col-sm-8">
        <input type="text" name="name_ru" value="{{$item->name_ru}}" placeholder="{{__('cities_1000.name_ru')}}" class="form-control input-sm" id="column_name_ru">
</div>
</div>

</div>


</div>
<!----   /cities_1000  ---->

<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Описание</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">На английском</label></div>
  <div class="col-sm-8"><input type="text" name="description" value="{{$item->description}}" placeholder="" class="form-control input-sm" id="column_description"></div>
</div>


<div class="form-group">
    <div class="col-sm-4">
        <label for="" class=" control-label">На русском</label>
    </div>
    <div class="col-sm-8">
        <input type="text" name="description_ru" value="{{$item->description_ru}}" placeholder="" class="form-control input-sm" id="column_description_ru">
</div>
</div>

</div>


</div>
<!----   /cities_1000  ---->


<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Информация от GeoNames:</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">Тип объекта</label></div>
  <div class="col-sm-8">
    <span style="display: inline-block; padding-top: 7px;">{{$geonamesClasses[$item->feature_class]}}</span>
  </div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">Описание объекта</label></div>
  <div class="col-sm-8">
    <span style="display: inline-block; padding-top: 7px;">{{$geonamesCodes[$item->feature_code]}}</span>
  </div>
</div>

</div>


</div>
<!----   /cities_1000  ---->



<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Падежи</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">genitive</label></div>
  <div class="col-sm-8"><input type="text" name="genitive" value="{{$item->genitive}}" class="form-control input-sm" placeholder="genitive"></div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">dative</label></div>
  <div class="col-sm-8"><input type="text" name="dative" value="{{$item->dative}}" class="form-control input-sm" placeholder="genitive"></div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">accusative</label></div>
  <div class="col-sm-8"><input type="text" name="accusative" value="{{$item->accusative}}" class="form-control input-sm" placeholder="accusative"></div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">instrumental</label></div>
  <div class="col-sm-8"><input type="text" name="instrumental" value="{{$item->instrumental}}" class="form-control input-sm" placeholder="instrumental"></div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">prepositional</label></div>
  <div class="col-sm-8"><input type="text" name="prepositional" value="{{$item->prepositional}}" class="form-control input-sm" placeholder="prepositional"></div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">without diacritics</label></div>
  <div class="col-sm-8"><input type="text" name="without_diacritics" value="{{$item->without_diacritics}}" class="form-control input-sm" placeholder="without_diacritics"></div>
</div>




</div>


</div>
<!----   /cities_1000  ---->



<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Координаты</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class=" control-label">longitude</label>
    </div>
    <div class="col-sm-8">
        <input type="text" name="longitude" value="{{$item->longitude}}" placeholder="{{__('cities_1000.longitude')}}" class="form-control" id="column_longitude">
    </div>
</div>

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class=" control-label">latitude</label>
    </div>
    <div class="col-sm-8">
        <input type="text" name="latitude" value="{{$item->latitude}}" placeholder="{{__('cities_1000.latitude')}}" class="form-control" id="column_latitude">
    </div>
</div>

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class=" control-label">На карте</label>
    </div>
    <div class="col-sm-8">
       @php $lat = '@' .$item->latitude; @endphp
        <span style="display: inline-block; padding-top: 7px;"><a href="https://www.google.com/maps/{{$lat}},{{$item->longitude}},3000m/data=!3m1!1e3?hl=ru" target="_blank"><i class="fa fa-fw fa-map-marker"></i></a></span>
    </div>
</div>

</div>


</div>
<!----   /cities_1000  ---->





<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Население:</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="number" name="population" value="{{$item->population}}" placeholder="{{__('cities_1000.population')}}" id="column_population" class="form-control"  >

                                </div>
</div>


</div>
<!----   /cities_1000  ---->


    </div>
    
    <div class="col-md-6">
       

<!----   cities_1000  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('cities_1000.modification_date') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
{{$item->modification_date}}
</div>

@if($item->custom_edited == 1)
<div class="box-footer bg-green">
    <span class="text-white"><i class="fa fa-fw fa-star-o"></i> Этот город был отредактирован вручную</span>
</div>
@endif

</div>

       
<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Базы данных</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class="control-label">WikiData entity</label>
    </div>
    <div class="col-sm-4">
<input type="text" name="wiki_entity" value="{{$item->wiki_entity}}" placeholder="{{__('cities_1000.wiki_entity')}}" class="form-control input-sm" id="column_wiki_entity">
    </div>
    <div class="col-sm-4">
@if($item->wiki_entity != null)<span style="display: inline-block; padding-top: 7px;"><a href="https://www.wikidata.org/wiki/{{$item->wiki_entity}}" target="_blank">{{$item->wiki_entity}}</a></span>@else <span style="display: inline-block; padding-top: 7px;">Пример: Q64</span> @endif 
    </div>
    
</div>


<div class="form-group">
    <div class="col-sm-4">
    <label for="" class="control-label">GeoNames ID</label>
    </div>
    <div class="col-sm-8"><span style="display: inline-block; padding-top: 7px;">{{$item->geonameid}}</span></div>
</div>


</div>

</div>
<!----   /cities_1000  ----> 
       
       
<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Коды</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class="control-label">IATA страны:</label>
    </div>
    <div class="col-sm-8"><input type="text" name="country_code" value="{{$item->country_code}}" placeholder="{{__('cities_1000.country_code')}}" class="form-control input-sm" id="column_country_code"></div>
</div>

<div class="form-group">
    <div class="col-sm-4">
    <label for="" class="control-label">IATA города:</label>
    </div>
    <div class="col-sm-8"><input type="text" name="iata_code" value="{{$item->iata_code}}" placeholder="IATA код города" class="form-control input-sm" id="column_iata_code"></div>
</div>


</div>


</div>
<!----   /cities_1000  ---->
       


</div>
<!----   /cities_1000  ---->
      
   
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('cities_1000.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('cities_1000.save') }}">
            </div>
        </div>
    </div>
</div>
<!-- /ACTION BUTTONS -->


<!-- Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body">
      <h5 class="modal-title" id="delete_confirmation">{{ __('cities_1000.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('cities_1000.delete') }}">
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