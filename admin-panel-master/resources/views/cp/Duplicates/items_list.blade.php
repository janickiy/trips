@extends('cp.cp_tk')

@section('content_title') Дубликаты RU и US @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li class="active">Дубликаты:</li>
</ol>
@endsection

@section('content')


<!---- CONTENT: ---->
<div class="row">
    <div class="col-xs-12">
        <div class="box">

@forelse($items as $item)
    @if($item->country_code == 'RU')
    <h2 class="text-center">{{$item->name_ru}}</h2> 
    
    
    @elseif($item->country_code == 'US')
    <h2 class="text-center">{{$item->name}}</h2>
    @else
    @endif
    
    
    
<table class="table table-striped tr-middle">
        <thead>
            <th>ID</th>
            <th>{{__('region.name_en')}}</th>
            <th>{{__('region.name_ru')}}</th>
            <th>Регион</th>
            <th>Wiki ID</th>

        </thead>
        

        
        
<?php 
    if($item->country_code == 'RU') {
        $cities = \App\City::where('name_ru', $item->name_ru)->get(['id', 'name', 'name_ru', 'wiki_entity', 'region_id']);
    } else if($item->country_code == 'US') {
        $cities = \App\City::where('name', $item->name)->get(['id', 'name', 'name_ru', 'wiki_entity', 'region_id']);
    } else {
        $cities = \App\City::where('name', $item->name)->get(['id', 'name', 'name_ru', 'wiki_entity', 'region_id']);
    }
?>
@forelse($cities as $city)
<tr>
    <td><a href="{{URL::to('/')}}//cp/cities/edit/{{$city->id}}">{{$city->id}}</td>
    <td>{{$city->name}}</td>
    <td>{{$city->name_ru}}</td>
<td>
@if($city->region_id > 0) 
    <?php $region = \App\Region::where('id', $city->region_id)->first(); ?>

    @if($item->country_code == 'RU')
        <a href="{{URL::to('/')}}/cp/region/edit/{{$region->id}}">{{$region->name_ru}}</a>
    
    @elseif($item->country_code == 'US')
        <a href="{{URL::to('/')}}/cp/region/edit/{{$region->id}}">{{$region->name_en}}</a>
    @else
    @endif

    
@endif
</td>
    <td>@if($city->wiki_entity != null)
        <a href="https://www.wikidata.org/wiki/{{$city->wiki_entity}}" target="_blank">{{$city->wiki_entity}}</a>
    
@endif
    </td>
    
</tr>

@empty
@endforelse
</table> 

@empty
   
@endforelse


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