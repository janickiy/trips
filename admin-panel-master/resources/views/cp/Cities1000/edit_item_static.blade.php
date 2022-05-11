@extends('cp.cp_tk')

@section('content_title') {{ __('cities_1000.edit_cities_1000') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/cities" id="items_list">Объединенные города</a></li>
    <li class="active">{{ __('cities_1000.edit_cities_1000') }} #{{$item->id}}:</li>
</ol>
@endsection

@section('content')

<?php
    $endpoint = env('STATIC_ENDPOINT');
?>



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
    <span style="display: inline-block; padding-top: 7px;">({{$item->feature_class}}) - {{$geonamesClasses[$item->feature_class]}}</span>
  </div>
</div>

<div class="form-group">
  <div class="col-sm-4"><label for="" class=" control-label">Описание объекта</label></div>
  <div class="col-sm-8">
    <span style="display: inline-block; padding-top: 7px;">({{$item->feature_code}}) - {{$geonamesCodes[$item->feature_code]}}</span>
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
       

<!----   cities_1000  ---->
<div class="box box-success form-horizontal" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">

        <!--- Essense:  --->
        <h3 class="box-title">Фото:</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
        
    </div>

    <div class="box-body">
        <p class="text-center" id="preview_exists"><img src="{{$endpoint}}/pics/{{$item->id}}.jpg" style="max-height: 250px;" id="preview_photo"></p>
        <p class="text-center" id="preview_not_exists" style="display: none;">Нет фото.</p>
    </div>
    <div class="box-footer">
        <!--<a href="#" class="btn btn-sm btn-default btn-flat change-photo" data-toggle="modal" data-target="#modal-default" city_id="{{$item->id}}">Заменить фото</a>-->
        <p><a href="#" class="btn btn-sm btn-default btn-flat upload_input" city_id="{{$item->id}}">Заменить фото</a>
        <a href="#" class="btn btn-sm btn-danger btn-flat delete-photo-btn" city_id="{{$item->id}}">Удалить фото</a></p>
        <p>server: {{$endpoint}}</p>
        
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


<form method="post" action="{{URL::to("/")}}/upload_city_photo" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" name="city_id" id="city_id" value="">
<div class="modal fade" id="modal-default" style="display: none;">

 
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Заменить фото города ID:#<span class="" id="city_id_to_photo_change"></span></h4>
      </div>
<div class="modal-body" id="preview_body">
        <p class="text-center" id="image_exists"><img src="" style="max-height: 250px;" id="last_photo"></p>
        <p class="text-center" id="image_not_exists">Нет фото.</p>
       
<div class="form-group">
  <label for="exampleInputFile">Выберите файл:</label>
  <input type="file" name="photo" id="foo" accept="image/*">
  <p class="help-block">Только .jpg</p>
</div>


</div>
<div class="modal-body text-center" id="loader_body" style="display: none;">
    <p>Не закрывайте окно. Идет загрузка...</p>
    <i class="fa fa-refresh fa-spin"></i>
    
</div>
<div class="modal-body text-center" id="result_body" style="display: none;">
   
</div>

    <!-- /.modal-content -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-success" id="change">Заменить</button>
      </div>
    
    
  </div>
  <!-- /.modal-dialog -->
</div>
</form>


<form method="post" action="http://the-apps.ru/api/upload_city_photo" autocomplete="off" enctype="multipart/form-data" id="form_id_{{$item->id}}">
{{ csrf_field() }}
    <input type="hidden" name="city_id" id="city_id" value="{{$item->id}}">
<div style="display: none;">
    <input type="file" name="photo" id="input_photo_city_id_{{$item->id}}" accept="image/*" class="input_photo" city_id="{{$item->id}}">
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

$('#preview_photo').on('error', function(){
    $('#preview_exists').hide();
    $('#preview_not_exists').show();
});

/*
$('body').on('click', '.change-photo', function() {
    $('#preview_body').show();
    $('.modal-footer').show();
    $('#loader_body').hide();
    $('#result_body').hide();
    $('#image_exists').show();
    $('#image_not_exists').hide();
    
    $('#last_photo').attr('src', 'http://the-apps.ru/storage/city_photos/' + $(this).attr('city_id') + '.jpg');
    

$('#last_photo').on('error', function(){
    $('#image_exists').hide();
    $('#image_not_exists').show();
});

    $('#city_id_to_photo_change').text($(this).attr('city_id'));
    $('#city_id').val($(this).attr('city_id'));
});

$('body').on('click', '#change', function() {
    
    $('#loader_body').show();
    $('#preview_body').hide();
    $('.modal-footer').hide();
    
    var formData = new FormData();
    formData.append('photo', $('#foo').prop('files')[0]);
    formData.append('city_id', $('#city_id').val());
    
    $.ajax({
        type: 'post',
        url: 'http://the-apps.ru/api/upload_city_photo',
        cache: false,
        contentType: false, // important
        processData: false, // important
        headers: {
            // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData
    })
    .done (function (response) {
        $('#loader_body').hide();
        $('#result_body').show();
        $('#result_body').text(response.message);
        
        $('#preview_photo').attr('src', $('#preview_photo').attr('src') + "?timestamp=" + new Date().getTime());

    }).always(function (response) {
        console.log(response);
        console.log(response.message);
    });
    

});
*/
















/*
    Кнопка выбора файла:
*/
$('body').on('click', '.upload_input', function() {  
    $('#input_photo_city_id_' + $(this).attr('city_id')).click();
    return false;
});

$('.input_photo').change(function(){
    
    var city_id = $(this).attr('city_id');
    
    console.log('change: ' + city_id);
    
    var formData = new FormData();
    formData.append('file', $('#input_photo_city_id_' + city_id).prop('files')[0]);
    formData.append('city_id', city_id);
    
    $.ajax({
        type: 'post',
        url: '{{URL::to("/")}}/api/pics',
        cache: false,
        contentType: false, // important
        processData: false, // important
        data: formData
    })
    .done (function (response) {
        $('#loader_body').hide();
        $('#result_body').show();
        $('#result_body').text(response.message);
        
        $('#photo_id_' + city_id).attr('src', $('#photo_id_' + city_id).attr('src') + "?timestamp=" + new Date().getTime());

        location.reload();

    }).always(function (response) {
        console.log(response);
        console.log(response.message);
    });
    
});


/*
    Удалить фото города с сервера статик:
*/
$('body').on('click', '.delete-photo-btn', function() {
    
    var cityID = $(this).attr('city_id');
    
    $.ajax({
        type: 'delete',
        url: '{{URL::to("/")}}/api/pics',
        data: {
            id: cityID
        }
    })
    .done (function (response) {
        console.log(response);
        location.reload();
    });
    
    return false;
});





</script>
@endsection