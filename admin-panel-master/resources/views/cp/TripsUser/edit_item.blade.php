@extends('cp.cp_tk')

@section('content_title'){{'Редактировать пользователя в приложении Trips'}} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/trips_user" id="items_list">{{ __('user.user') }}</a></li>
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

<form method="post" action="{{URL::to('/')}}/cp/trips_user/edit" autocomplete="off" enctype="multipart/form-data">
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
    <h3 class="box-title">first_name</h3>
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        <input type="text" name="first_name" value="{{$item->first_name}}" placeholder="first_name" class="form-control input-sm" id="column_first_name"  >
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
        <input type="text" name="last_name" value="{{$item->last_name}}" placeholder="last_name" class="form-control input-sm" id="column_last_name"  >
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
        <input type="text" name="username" value="{{$item->username}}" placeholder="username" class="form-control input-sm" id="column_username" required>
    </div>
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
<div class="form-group">
                                
<input type="text" name="email" value="{{$item->email}}" placeholder="{{__('user.email')}}" class="form-control input-sm" id="column_email" required >

                                </div>
</div>


</div>
<!----   /user  ---->




    </div>
    
    <div class="col-md-4">

<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Статус</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<select name="deleted" id="column_deleted" class="form-control" >
<option value="0" @if($item->deleted == 0) selected @endif>Активен</option>
<option value="1" @if($item->deleted == 1) selected @endif>Удален</option>
</select>

                                </div>
</div>


</div>
<!----   /user  ---->
    
    
    
<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Артефакты</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
{{count($item->artifacts)}}
</div>


</div>
<!----   /user  ----> 
    
    
<!----   user  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Соц. сети</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
    @forelse($item->socialAccounts as $socialAccount)
        @if($socialAccount->provider == 'facebook')
            <i class="fa fa-facebook-official" aria-hidden="true"></i>
        @elseif($socialAccount->provider == 'google')
            <i class="fa fa-google" aria-hidden="true"></i>
        @elseif($socialAccount->provider == 'apple')
            <i class="fa fa-apple" aria-hidden="true"></i>
        @elseif($socialAccount->provider == 'github')
            <i class="fa fa-github" aria-hidden="true"></i>
        @else
        @endif
    @empty 
    @endforelse
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
    <div class="form-group">{{$item->created_at}}</div>
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
<div class="form-group">{{$item->updated_at}}</div>
</div>


</div>
<!----   /user  ---->    
    
    
    
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
            <!--
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('user.delete') }}
                </button>
                --->
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