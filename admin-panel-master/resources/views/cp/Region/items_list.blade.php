@extends('cp.cp_tk')

@section('content_title') {{ __('region.region') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">{{ __('region.region') }}:</li>
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
            <a href="{{URL::to('/')}}/cp/region/add" class="btn btn-success" id="add_new">
                {{ __('region.create_new') }}
            </a> 
        </p>
    <!---- /ADD NEW BUTTON ---->
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">

    <!---- SEARCH FIELD: ---->
    <form method="get" action="" autocomplete="off" id="search_region">
        <div class="input-group">
            <input placeholder="{{ __('region.search_region') }}" type="text" name="q" class="form-control" value="{{ app('request')->input('q') }}">
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
<tr><th>{{__('region.id')}}</th>

<th>{{__('region.wiki_entity')}}</th>
<th>{{__('region.name_en')}}</th>
<th>{{__('region.name_ru')}}</th>
<th>Страна</th>
<th>Модерация</th>
<th>Обновлено</th>
<th><i class="fa fa-fw fa-edit"></i> ред.</th>
<tr>

        </thead>
        
        <tbody>
@forelse($items as $item)
<tr>
    <td>{{$item->id}}</td>
    <td>
        @if($item->wiki_entity != null)
        <a href="https://www.wikidata.org/wiki/{{$item->wiki_entity}}">{{$item->wiki_entity}}</a>
        @endif

    </td>
    <td>{{$item->name_en}}</td>
    <td>{{$item->name_ru}}</td>

    <td>
        <a href="{{URL::to('/')}}/cp/country/edit/{{$item->country['id']}}">{{$item->country['name_ru']}}</a>
    </td>
    <td>
        @if($item->deleted_at != null)
            <span class="badge bg-red">Удалено</span>
        @endif  
    </td>
    <td>
        @if($item->deleted_at != null)
            {{$item->deleted_at}}
        @else 
            {{$item->updated_at}}
        @endif
    </td>
    <td>
        <a href="{{URL::to("/")}}/cp/region/edit/{{$item->id}}">
        <i class="fa fa-fw fa-edit"></i> ред.
        </a>
    </td>
</tr>
@empty
    <tr><td colspan="42">{{__('region.no_records')}}</td></tr>
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