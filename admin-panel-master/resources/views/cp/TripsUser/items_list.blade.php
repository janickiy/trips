@extends('cp.cp_tk')

@section('content_title'){{''}}Пользователи приложения Trips{{''}}@endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Список пользователей:</li>
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
    <!---- ADD NEW BUTTON: ---->
        <p>
            <a href="{{URL::to('/')}}/cp/trips_user/add" class="btn btn-success" id="add_new">
                {{ __('user.create_new') }}
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
           
            <span class="input-group-btn">
            <a href="{{URL::to('/')}}/cp/trips_user" id="cancel-search-btn" class="btn btn-danger">
                <i class="fa fa-fw fa-remove"></i>
            </a>
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
<th>first_name</th>
<th>last_name</th>
<th>username / e-mail</th>
<th>Соц. сети</th>
<th>Артефакты</th>
<th>Статус</th>
<th>{{__('user.created_at')}}</th>
<th><i class="fa fa-fw fa-edit"></i> {{__('user.edit')}}</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td>{{$item->first_name}}</td>
<td>{{$item->last_name}}</td>
<td>{{$item->username}}<br> {{$item->email}}</td>
<td>
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
</td>

<td>
{{count($item->artifacts)}}
</td>
<td>
    @if($item->deleted == 1)
        <span class="text-danger">Удален</span> 
    @else <span class="text-success">Активный</span> 
    @endif
</td>
<td>{{$item->created_at}}</td>
<td>
    <a href="{{URL::to("/")}}/cp/trips_user/edit/{{$item->id}}">
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



<!-- Экспорт -->
<form method="post" action="/cp/export_users_csv">
{{csrf_field()}}
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Экспорт в .CSV</h3>
    </div>
    <div class="box-body">
    
        <div class="form-group">
            <label>Введите ID:</label>
            <textarea name="ids" class="form-control" rows="3" placeholder="Оставьте поле пустым, чтобы экспортировать всю базу пользователей сразу. А чтобы экспортировать выбранных, введите по 1 ID на строку"></textarea>
        </div>
            
        <div style="display: flex; justify-content: space-between;">
            <div style="display: flex;">
            </div>
            <div style="">
                <input type="submit" class="btn btn-block btn-info btn-sm" id="export_btn" value="Экспортировать">
            </div>
        </div> 
       
    </div>
</div>
</form>
<!-- /Экспорт -->

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

/*
$('body').on('click', '#export_btn', function() {

    $.ajax({
        type: 'get',
        url: '/cp/export_users_csv',
        data: {},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done (function (response) {

    }).fail(function (response) {
        
    }).then(function(response){ 
        
    }).always(function (response) {
        console.log("export response", response);
    });
    
    return false;
});
*/
</script>
@endsection