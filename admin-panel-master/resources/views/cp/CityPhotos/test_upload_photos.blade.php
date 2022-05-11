@extends('cp.cp_tk')

@section('content_title') Тест загрузки фото @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Тест загрузки фото:</li>
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



<!---- CONTENT: ---->
<div class="row">
    <div class="col-md-12">
    
<form method="post" action="{{URL::to("/")}}/cp/test_upload_photos" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" name="city_id" id="city_id" value="99999999999999">
<input type="file" name="photo" accept=".jpg, .jpeg, .png"><br>
<input type="submit" name="submit" value="submit">

</form>
        
    </div>
</div>
<!---- /CONTENT ---->



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

</script>
@endsection