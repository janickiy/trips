@extends('cp.cp_tk')

@section('content_title') Фотографии городов @endsection

@section('content_description')

@endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Фотографии городов:</li>
</ol>
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
    
   
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

   <p>
    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_cities_1000">
        <div class="input-group">
<span class="input-group-addon">
  <label style="margin-bottom: 0px; margin-right: 10px;">
    <input type="checkbox" name="no_photo" style="float: left; margin-right: 3px;" @if(app('request')->has('no_photo') and app('request')->input('no_photo') == 'on') checked @endif> 
    <div style="margin-top: 2px; ">Без фото</div>
  </label>
</span>
            <input placeholder="Поиск городов" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
            <a href="{{URL::to('/')}}/cp/city_photos" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
           </span>
        </div>
    </form>
    <!---- /SEARCH FIELD ---->
   </p>
   

    </div>
</div>

<style>
.input-group-addon input[type=checkbox], .input-group-addon input[type=radio] {
     margin-top: 2px !important;
}
</style>



<!---- CONTENT: ---->
<div class="row">
@forelse($items as $item)

<div class="col-xs-12 col-md-6 col-md-6 col-lg-4">
    <div class="box box-primary">
    
        <div class="box-header with-border text-center">
            <h3 class="box-title">{{$item->name}}</h3>
        </div>
        
        <div class="box-body text-center" style="overflow: hidden;">
        
<?php 
    $imageUrl = '';

    if($item->has_photo == 1) {
        $imageUrl = URL::to('/') . '/storage/city_photos/' . $item->id . '.jpg';
    } else {
        $imageUrl = URL::to('/') . '/images/no-city-photo.jpg';
    }
?>
<div class="one_good_image_block bg-light">
    <img src="{{$imageUrl}}" class="city_photo one_good_image rounded" id="photo_id_{{$item->id}}">
</div>
        
            <ul class="products-list product-list-in-box text-left">

                <li class="item">

    <b>На русском:</b> {{$item->name_ru}}<br>
    <b>На английском:</b> {{$item->name}}<br>
    <b>IATA:</b> {{$item->iata_code}}<br>
    <b>Код страны:</b> {{$item->country_code}}<br>

                </li>
            </ul>
        </div>
   
    <div class="box-footer">

<a href="{{URL::to('/')}}/cp/cities/edit/{{$item->id}}" class="btn btn-sm btn-default btn-flat">Ред.</a> </a>
<!--<a href="#" class="btn btn-sm btn-default btn-flat change-photo" data-toggle="modal" data-target="#modal-default" city_id="{{$item->id}}">Заменить фото</a>-->
<a href="#" class="btn btn-sm btn-default btn-flat upload_input" city_id="{{$item->id}}">Заменить фото</a>

    </div>
    <!-- /.box-footer -->
    </div>
</div>


<!-- форма загрузки изображения: --->
<form   method="post" 
        action="{{URL::to("/")}}/cp/upload_city_photo" 
        autocomplete="off" 
        enctype="multipart/form-data" 
        id="form_id_{{$item->id}}"
>
{{ csrf_field() }}
    <input type="hidden" name="city_id" id="city_id" value="{{$item->id}}">
<div style="display: none;">
    <input type="file" name="photo" id="input_photo_city_id_{{$item->id}}" accept="image/*" class="input_photo" city_id="{{$item->id}}">
    <input type="submit" name="submit" id="submit_upload_form_{{$item->id}}">
</div>

</form>
<!-- /форма загрузки изображения --->

@empty
@endforelse
    
</div>
<!---- /CONTENT ---->

<div class="card-footer d-block">
    <div class="text-left">{{$items->links("cp.cp_pagination")}}</div>
</div>



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
        <p class="text-center"><img src="" style="max-height: 250px;" id="last_photo"></p>
       
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
</div>
</form>




<div class="card-footer d-block">
    <div class="text-right">
        <a href="#" class="btn btn-sm btn-default btn-flat" id="load_photo_btn">Просканировать папку и добавить новые фото</a>
        <span class="" id="folder_scanning" style="display: none;"><i class="fa fa-refresh fa-spin"></i> Идет сканирование хранилища фотографий...</span>
        
    </div>
</div>


<style>
.one_good_image_block {
    width: 343px;
    height: 160px;
    overflow: hidden;
    display: inline-block;
}

.one_good_image {
    width: 100%;
    height: 100%;
    /* object-fit: contain; */
    object-fit: cover;
}

.product-list-in-box>.item {
    border-bottom: none !important;
}



@media (min-width:992px) {.one_good_image_block { width: 260px; } }
@media (min-width:1045px) {.one_good_image_block { width: 350px; } }
@media (min-width:1200px) {.one_good_image_block { width: 260px; } }
@media (min-width:1300px) {.one_good_image_block { width: 280px; } }
@media (min-width:1400px) {.one_good_image_block { width: 335px; } }
@media (min-width:1500px) {.one_good_image_block { width: 343px; } }
@media (min-width:1600px) {.one_good_image_block { width: 373px; } }
@media (min-width:1700px) {.one_good_image_block { width: 393px; } }


</style>
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



$('body').on('click', '.change-photo', function() {
    $('#preview_body').show();
    $('.modal-footer').show();
    $('#loader_body').hide();
    $('#result_body').hide();
    
    $('#last_photo').attr('src', '{{URL::to("/")}}/storage/city_photos/' + $(this).attr('city_id') + '.jpg');
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
        
        $('#photo_id_' + $('#city_id').val()).attr('src', $('#photo_id_' + $('#city_id').val()).attr('src') + "?timestamp=" + new Date().getTime());

    }).always(function (response) {
        console.log(response);
        console.log(response.message);
    });
    

});


// Кнопка "заменить фото"
$('body').on('click', '.upload_input', function() { 
    $('#input_photo_city_id_' + $(this).attr('city_id')).click();
    return false;
});


/**
 *  Обработка загрузки фото:
 */
$('.input_photo').change(function() {
    var city_id = $(this).attr('city_id');
    
    uploadToLocalStorage(city_id);
});


/**
 *  Загрузка изображения на тот же сервер в админке:
 */
function uploadToLocalStorage(city_id)
{

    var formData = new FormData();
    formData.append('photo', $('#input_photo_city_id_' + city_id).prop('files')[0]);
    formData.append('city_id', city_id);
    
    $.ajax({
        type: 'post',
        url: '/cp/upload_city_photo',
        cache: false,
        contentType: false, // important
        processData: false, // important
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
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
    
}


function uploadToRemoveServer() 
{
    var city_id = $(this).attr('city_id');
    
    console.log('change: ' + city_id);
    
    var formData = new FormData();
    formData.append('photo', $('#input_photo_city_id_' + city_id).prop('files')[0]);
    formData.append('city_id', city_id);
    
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
        
        $('#photo_id_' + city_id).attr('src', $('#photo_id_' + city_id).attr('src') + "?timestamp=" + new Date().getTime());
        
        
        location.reload();

    }).always(function (response) {
        console.log(response);
        console.log(response.message);
    });  
}


/**
 *  Сканирование фотографий в локальном хранилище:
 */
$('body').on('click', '#load_photo_btn', function() {
    
    $('#load_photo_btn').hide();
    $('#folder_scanning').show();
    
    $.ajax({
        type: 'get',
        url: '{{URL::to("/")}}/api/add_new_city_photos_from_storage',
        data: {}
    })
    .done (function (response) {
        
    }).always(function (response) {
        //console.log(response);
        location.reload();
    });
    
    return false;
});
</script>
@endsection