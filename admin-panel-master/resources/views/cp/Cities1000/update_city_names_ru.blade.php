@extends('cp.cp_tk')

@section('content_title') Загрузка city name_ru @endsection

@section('content_breadcrumb')
@endsection

@section('content')



<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">

    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">



    </div>
</div>




@endsection

@section('scripts')
<script>

setInterval(function() {
    //
    $.ajax({
        type: 'get',
        url: "{{URL::to('/')}}/api/get_name_ru_null",
        data: {},
    })
    .done (function (response) {
        console.log(response);
    });
    

}, 3000);

</script>
@endsection