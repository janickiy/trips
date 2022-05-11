@extends('cp.cp_tk')

@section('content_title') {{ __('travelpayout.edit_travelpayout') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/travelpayouts" id="items_list">{{ __('travelpayout.travelpayout') }}</a></li>
    <li class="active">{{ __('travelpayout.edit_travelpayout') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/travelpayout/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->



<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.name_en') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="name_en" value="{{$item->name_en}}" placeholder="{{__('travelpayout.name_en')}}" class="form-control input-sm" id="column_name_en"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.name_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="name_ru" value="{{$item->name_ru}}" placeholder="{{__('travelpayout.name_ru')}}" class="form-control input-sm" id="column_name_ru"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.country_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="country_code" value="{{$item->country_code}}" placeholder="{{__('travelpayout.country_code')}}" class="form-control input-sm" id="column_country_code"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.iata_code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="iata_code" value="{{$item->iata_code}}" placeholder="{{__('travelpayout.iata_code')}}" class="form-control input-sm" id="column_iata_code"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.lon') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="lon" value="{{$item->lon}}" placeholder="{{__('travelpayout.lon')}}" class="form-control input-sm" id="column_lon"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.lat') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="lat" value="{{$item->lat}}" placeholder="{{__('travelpayout.lat')}}" class="form-control input-sm" id="column_lat"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.vi') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="vi" value="{{$item->vi}}" placeholder="{{__('travelpayout.vi')}}" class="form-control input-sm" id="column_vi"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.tv') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="tv" value="{{$item->tv}}" placeholder="{{__('travelpayout.tv')}}" class="form-control input-sm" id="column_tv"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.ro') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="ro" value="{{$item->ro}}" placeholder="{{__('travelpayout.ro')}}" class="form-control input-sm" id="column_ro"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.pr') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="pr" value="{{$item->pr}}" placeholder="{{__('travelpayout.pr')}}" class="form-control input-sm" id="column_pr"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->

<!----   travelpayout  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('travelpayout.da') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" disabled name="da" value="{{$item->da}}" placeholder="{{__('travelpayout.da')}}" class="form-control input-sm" id="column_da"  >

                                </div>
</div>


</div>
<!----   /travelpayout  ---->



    </div>
    
    <div class="col-md-4">
        
        
        
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<!--
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                    {{ __('travelpayout.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('travelpayout.save') }}">
            </div>
        </div>
    </div>
</div>
 -->
<!-- /ACTION BUTTONS -->


<!-- Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body">
      <h5 class="modal-title" id="delete_confirmation">{{ __('travelpayout.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('travelpayout.delete') }}">
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