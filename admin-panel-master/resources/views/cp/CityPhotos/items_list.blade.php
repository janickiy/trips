@extends('cp.cp_tk')

@section('content_title') Фотографии городов @endsection

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

<!----
<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
    
        <p>
            <a href="#" class="btn btn-success" id="add_new">
                {{ __('country.create_new') }}
            </a> 
        </p>
   
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

   
    <form method="get" action="" autocomplete="off" id="search_country">
        <div class="input-group">
            <input placeholder="Поиск фотографий" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
           </span>
        </div>
    </form>
   

    </div>
</div>
---->



<!---- CONTENT: ---->
<div class="row">
@forelse($items as $item)

<?php 
    $parts = explode('/', $item);
    $photoID = explode('.', $parts[count($parts) - 1])[0];
    $city = \App\City::where(['id' => $photoID])->first(); 
?>
<div class="col-xs-3">
    <div class="box box-primary">
    
        <div class="box-header with-border text-center">
            <h3 class="box-title">@if($city != null){{$city->name}}@endif</h3>
        </div>
        
        <div class="box-body">
            <ul class="products-list product-list-in-box">
                <li class="item text-center" style="height: 180px; overflow: hidden;">
                    <img src="{{URL::to('/')}}/{{str_replace('public/', '', $item)}}" class="" style="width: 100%; max-height: 200px;" id="photo_id_{{$photoID}}">
                </li>
                <li class="item">
@if($city != null)
    <b>На русском:</b> {{$city->name_ru}}<br>
    <b>На английском:</b> {{$city->name}}<br>
    <b>IATA:</b> {{$city->iata_code}}<br>
    <b>Код страны:</b> {{$city->country_code}}<br>
@endif
                </li>
            </ul>
        </div>
   
    <div class="box-footer">
@if($city != null)
<a href="{{URL::to('/')}}/cp/cities/edit/{{$city->id}}" class="btn btn-sm btn-default btn-flat">Ред.</a> </a>
<a href="#" class="btn btn-sm btn-default btn-flat change-photo" data-toggle="modal" data-target="#modal-default" city_id="{{$city->id}}">Заменить фото</a>
@endif
    </div>
    <!-- /.box-footer -->
    </div>
</div>
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
  <input type="file" name="photo" id="foo" accept=".jpg, .jpeg">
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
        url: '{{URL::to("/")}}/cp/upload_city_photo',
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
        
        $('#photo_id_' + $('#city_id').val()).attr('src', $('#photo_id_' + $('#city_id').val()).attr('src') + "?timestamp=" + new Date().getTime());

    }).always(function (response) {
        console.log(response);
        console.log(response.message);
    });
    

});

</script>
@endsection