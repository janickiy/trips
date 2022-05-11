@extends('cp.cp_tk')

@section('content_title') Города из Travelpayouts @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Города из Travelpayouts:</li>
</ol>
@endsection

@section('content_description')
Здесь отображаются города, скачанные из Travelpayouts. Только просмотр, нет редактирования. 
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
    <!---- 
        <p>
            <a href="{{URL::to('/')}}/cp/travelpayout/add" class="btn btn-success" id="add_new">
                {{ __('travelpayout.create_new') }}
            </a> 
        </p>
     ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_travelpayout">
    <p>
        <div class="input-group">
            <input placeholder="{{ __('travelpayout.search_travelpayout') }}" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
            <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
           </span>
        </div>
        </p>
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
<tr><th>{{__('travelpayout.id')}}</th>
<th>{{__('travelpayout.name_en')}}</th>
<th>{{__('travelpayout.name_ru')}}</th>
<th>{{__('travelpayout.country_code')}}</th>
<th>{{__('travelpayout.iata_code')}}</th>
<th>{{__('travelpayout.lon')}}</th>
<th>{{__('travelpayout.lat')}}</th>
<th><i class="fa fa-map-o"></i></th>
<th class="text-center">Падежи</th>
<td><i class="fa fa-fw fa-eye"></i></td>
<!--- <th><i class="fa fa-fw fa-edit"></i> {{__('travelpayout.edit')}}</th> --->
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
    <tr><td>{{$item->id}}</td>
<td>{{$item->name_en}}</td>
<td>{{$item->name_ru}}</td>
<td><a href="{{URL::to('/')}}/cp/travelpayouts">{{$item->country_code}}</a></td> 
<td>{{$item->iata_code}}</td>
<td>{{$item->lon}}</td>
<td>{{$item->lat}}</td>
@php $lat = '@' .$item->lat; @endphp
<td><a href="https://www.google.com/maps/{{$lat}},{{$item->lon}},3000m/data=!3m1!1e3?hl=ru" target="_blank"><i class="fa fa-fw fa-map-marker"></i></a></td>

<td class="text-center">@if($item->vi != null)<i class="fa fa-circle text-success"></i>@endif</td>
<td>
<a href="{{URL::to("/")}}/cp/travelpayouts/edit/{{$item->id}}">
<i class="fa fa-fw fa-eye"></i>
</a>
</td>
<!---
<td>
    <a href="{{URL::to("/")}}/cp/travelpayout/edit/{{$item->id}}">
    <i class="fa fa-fw fa-edit"></i> {{__('travelpayout.edit')}}
    </a></td>
</tr>
--->
@empty
    <tr><td colspan="42">{{__('travelpayout.no_records')}}</td></tr>
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