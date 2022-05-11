@extends('cp.cp_tk')

@section('content_title') {{ __('user.user') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">{{ __('user.user') }}:</li>
</ol>
@endsection

@section('content_description')
В админку могут попасть только те пользователи, для которых указана роль "admin".
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
    <!---- ADD NEW BUTTON: ---->
        <p>
            <a href="{{URL::to('/')}}/cp/user/add" class="btn btn-success" id="add_new">
                Создать пользователя
            </a> 
        </p>
    <!---- /ADD NEW BUTTON ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_user">
        <div class="input-group">
            <input placeholder="{{ __('user.search_user') }}" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
           </span>
        </div>
    </form>
    <!---- /SEARCH FIELD ---->

    </div>
</div>




<!---- CONTENT: ---->
<div class="row">
    <div class="col-xs-12">
        <div class="box">

<div class="box-body table-responsive no-padding">
    <table class="table table-striped tr-middle">
        <thead>
<tr><th>{{__('user.id')}}</th>
<th>{{__('user.name')}}</th>
<th>{{__('user.email')}}</th>
<th>{{__('user.lang')}}</th>
<th>{{__('user.role')}}</th>
<th><i class="fa fa-fw fa-edit"></i> {{__('user.edit')}}</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td>{{$item->name}}</td>
<td>{{$item->email}}</td>
<td>{{$item->lang}}</td>
<td>{{$item->role["name"]}}</td>
<td>
    <a href="{{URL::to("/")}}/cp/user/edit/{{$item->id}}">
    <i class="fa fa-fw fa-edit"></i> {{__('user.edit')}}
    </a></td>
</tr>
@empty
    <tr><td colspan="42">{{__('user.no_records')}}</td></tr>
@endforelse

        </tbody>
        
    </table>
</div>

        </div>
    </div>
</div>
<!---- /CONTENT ---->


<div class="card-footer d-block">
    <div class="text-left">{{$items->links("cp.cp_pagination")}}</div>
</div>

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