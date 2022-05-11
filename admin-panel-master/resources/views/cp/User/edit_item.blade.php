@extends('cp.cp_tk')

@section('content_title') {{ __('user.edit_user') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/user" id="items_list">{{ __('user.user') }}</a></li>
    <li class="active">{{ __('user.edit_user') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/user/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->


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
<div class="form-group"><input type="text" name="name" value="{{$item->name}}" placeholder="{{__('user.name')}}" class="form-control input-sm" id="column_name" required ></div>
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
<div class="form-group"><input type="email" name="email" class="form-control" value="{{$item->email}}" id="column_email" placeholder="{{__('user.email')}}"></div>
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
<div class="form-group"><input type="text" name="created_at" value="{{$item->created_at}}" placeholder="{{__('user.created_at')}}" class="form-control input-sm" id="column_created_at"  disabled></div>
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
<div class="form-group"><input type="text" name="updated_at" value="{{$item->updated_at}}" placeholder="{{__('user.updated_at')}}" class="form-control input-sm" id="column_updated_at"  disabled></div>
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
<div class="form-group">
@if($item->image == null) 
 <input type="file" name="image" id="column_image"> 
 <p class="help-block">{{__("cp.image_accepted_extensions")}}</p> 
 @else <p><img src="{{URL::to("/")}}/{{$item->image}}" style="width: 100%;"></p>

<div class="checkbox">
<label><input type="checkbox" name="delete_image" id="delete_column_image" class="">{{__("cp.delete")}} image</label> 

</div> @endif</div>
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
<div class="form-group"><input type="text" name="ip" value="{{$item->ip}}" placeholder="{{__('user.ip')}}" class="form-control input-sm" id="column_ip"  ></div>
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
        <option value="{{$role->access_level}}" @if($item->role_id == $role->access_level) selected @endif>{{$role->name}}</option>
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
        <option value="en" @if($item->lang == "en") selected @endif >en</option>
        <option value="ru" @if($item->lang == "ru") selected @endif >ru</option>
        <option value="ua" @if($item->lang == "ua") selected @endif >ua</option>
</select>

</div>
</div>


</div>

        
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('user.new_password') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="password" class="form-control" id="column_new_password" name="new_password" placeholder="Пароль" value="">

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
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('user.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('user.save') }}">
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
      <h5 class="modal-title" id="delete_confirmation">{{ __('user.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('user.delete') }}">
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