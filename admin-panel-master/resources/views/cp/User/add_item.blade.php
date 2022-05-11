@extends('cp.cp_tk')

@section('content_title') {{ __('user.create_user') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/user" id="items_list">{{ __('user.user') }}</a></li>
    <li class="active">{{ __('user.create_user') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/user/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}

@if (Session::has('email_exists'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ Session::get('email_exists') }}</h4>
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
    <h3 class="box-title">{{ __('user.name') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="text" name="name" value="" placeholder="{{__('user.name')}}" class="form-control input-sm" id="column_name" required ></div>
</div>


</div>
<!----   /user  ---->

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
<div class="form-group"><input type="email" name="email" class="form-control" id="column_email" placeholder="{{__('user.email')}}"></div>
</div>


</div>
<!----   /user  ---->

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.password') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="password" class="form-control" id="column_password" name="password" placeholder="{{__('user.password')}}" ></div>
</div>


</div>
<!----   /user  ---->

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.created_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="text" name="created_at" value="" placeholder="{{__('user.created_at')}}" class="form-control input-sm" id="column_created_at"  disabled></div>
</div>


</div>
<!----   /user  ---->

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.updated_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="text" name="updated_at" value="" placeholder="{{__('user.updated_at')}}" class="form-control input-sm" id="column_updated_at"  disabled></div>
</div>


</div>
<!----   /user  ---->

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.image') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="file" name="image" id="column_image"> 
<p class="help-block">{{__("cp.image_accepted_extensions")}}</p></div>
</div>


</div>
<!----   /user  ---->

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.ip') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group"><input type="text" name="ip" value="" placeholder="{{__('user.ip')}}" class="form-control input-sm" id="column_ip"  ></div>
</div>


</div>
<!----   /user  ---->

    </div>
    
    <div class="col-md-4">
        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.role') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<select name="role_id" id="column_role_id" class="form-control">
    @forelse($roles as $role)
        <option value="{{$role->access_level}}" @if($role->access_level == 0) selected @endif>{{$role->name}}</option>
    @empty
    @endforelse
</select>

</div>
</div>


</div>

        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.lang') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<select name="lang" id="column_lang" class="form-control">
        <option value="en">en</option>
        <option value="ru">ru</option>
        <option value="ua">ua</option>
</select>

</div>
</div>


</div>

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