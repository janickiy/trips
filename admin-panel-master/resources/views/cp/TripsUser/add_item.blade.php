@extends('cp.cp_tk')

@section('content_title'){{'Создать пользователя в приложении Trips'}}@endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/trips_user" id="items_list">{{ __('user.user') }}</a></li>
    <li class="active">{{ __('user.create_user') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/trips_user/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}

@if (Session::has('email_exists'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Указанный e-mail уже зарегистрирован.</h4>
        </div>
    </div>
</div>
@endif

<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.email') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="email" value="" placeholder="{{__('user.email')}}" class="form-control input-sm" id="column_email" required>
</div>
</div>


</div>
<!----   /user  ---->





<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">
    <h3 class="box-title">username</h3>
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        <input type="text" name="username" value="" placeholder="username" class="form-control input-sm" id="column_username" required>
    </div>
</div>
</div>
<!----   /user  ---->


<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">
    <h3 class="box-title">first_name</h3>
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        <input type="text" name="first_name" value="" placeholder="first_name" class="form-control input-sm" id="column_first_name"  >
    </div>
</div>
</div>
<!----   /user  ---->


<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">
    <h3 class="box-title">last_name</h3>
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        <input type="text" name="last_name" value="" placeholder="last_name" class="form-control input-sm" id="column_last_name"  >
    </div>
</div>
</div>
<!----   /user  ---->


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
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('user.create') }}">
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