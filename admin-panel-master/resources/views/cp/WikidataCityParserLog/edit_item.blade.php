@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_city_parser_log.edit_wikidata_city_parser_log') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_city_parser_log" id="items_list">{{ __('wikidata_city_parser_log.wikidata_city_parser_log') }}</a></li>
    <li class="active">{{ __('wikidata_city_parser_log.edit_wikidata_city_parser_log') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/wikidata_city_parser_log/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->


<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.city_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="number" name="city_id" value="{{$item->city_id}}" placeholder="{{__('wikidata_city_parser_log.city_id')}}" id="column_city_id" class="form-control"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.category_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="category_id" value="{{$item->category_id}}" placeholder="{{__('wikidata_city_parser_log.category_id')}}" class="form-control input-sm" id="column_category_id"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.reason_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="reason_id" value="{{$item->reason_id}}" placeholder="{{__('wikidata_city_parser_log.reason_id')}}" class="form-control input-sm" id="column_reason_id"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.search_results') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="hidden" name="search_results" value="{{$item->search_results}}" id="column_search_results">

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.old_population') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="old_population" value="{{$item->old_population}}" placeholder="{{__('wikidata_city_parser_log.old_population')}}" class="form-control input-sm" id="column_old_population"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.query_time') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="query_time" value="{{$item->query_time}}" placeholder="{{__('wikidata_city_parser_log.query_time')}}" class="" id="column_query_time"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.created_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="hidden" name="created_at" value="{{$item->created_at}}" id="column_created_at">

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

<!----   wikidata_city_parser_log  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_city_parser_log.updated_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="updated_at" value="{{$item->updated_at}}" placeholder="{{__('wikidata_city_parser_log.updated_at')}}" class="form-control input-sm" id="column_updated_at"  >

                                </div>
</div>


</div>
<!----   /wikidata_city_parser_log  ---->

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
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('wikidata_city_parser_log.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_city_parser_log.save') }}">
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
      <h5 class="modal-title" id="delete_confirmation">{{ __('wikidata_city_parser_log.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('wikidata_city_parser_log.delete') }}">
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