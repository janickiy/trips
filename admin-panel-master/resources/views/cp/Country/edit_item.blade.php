@extends('cp.cp_tk')

@section('content_title') {{ __('country.edit_country') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/country" id="items_list">{{ __('country.country') }}</a></li>
    <li class="active">{{ __('country.edit_country') }} #{{$item->id}}:</li>
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

@if (Session::has('restore_item'))
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ Session::get('restore_item') }}</h4>
            @if (Session::has('details'))
            <p>{{ Session::get('details') }}</p>
            @endif
        </div>
    </div>
</div>
@endif

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

<form method="post" action="{{URL::to('/')}}/cp/country/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->


<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Название на английском</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="name_en" value="{{$item->name_en}}" placeholder="Название на английском" class="form-control input-sm" id="column_name" >

                                </div>
</div>


</div>
<!----   /country  ---->



<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Название на русском</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="name_ru" value="{{$item->name_ru}}" placeholder="Название на русском" class="form-control input-sm" id="column_name" required >

                                </div>
</div>


</div>
<!----   /country  ---->




<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.wiki_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="wiki_id" value="{{$item->wiki_id}}" placeholder="{{__('country.wiki_id')}}" class="form-control input-sm" id="column_wiki_id">

                                </div>
</div>


</div>
<!----   /country  ---->

<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.wiki_link') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="wiki_link" value="{{$item->wiki_link}}" placeholder="{{__('country.wiki_link')}}" class="form-control input-sm" id="column_wiki_link">

                                </div>
</div>


</div>
<!----   /country  ---->



<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Регион</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
                                
<input type="text" name="wiki_region_class_id" value="{{$item->wiki_region_class_id}}" placeholder="Пример Qxxx" class="form-control input-sm" id="column_wiki_region_class_id" >

                                </div>
</div>


</div>
<!----   /country  ---->




<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Падежи:</h3>
    </div>

<div class="box-body form-horizontal">

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">vi</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="vi" value="{{$item->vi}}">
    </div>
</div>

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">tv</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="tv" value="{{$item->tv}}">
    </div>
</div>

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">ro</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="ro" value="{{$item->ro}}">
    </div>
</div>

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">pr</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="pr" value="{{$item->pr}}">
    </div>
</div>

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">da</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="da" value="{{$item->da}}">
    </div>
</div>


</div>

</div>
<!----   /country  ---->




<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
    <div class="box-header ui-sortable-handle">
        <h3 class="box-title">Коды:</h3>
    </div>

<div class="box-body form-horizontal">

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Код страны:</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="code" value="{{$item->code}}">
    </div>
</div>

<div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Код валюты:</label>

    <div class="col-sm-10">
    <input type="text" class="form-control" name="currency" value="{{$item->currency}}">
    </div>
</div>



</div>

</div>
<!----   /country  ---->





    </div>
    
    <div class="col-md-4">
        
<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.moderated') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<select name="moderated" id="column_moderated" class="form-control" >
    <option value="0" @if($item->moderated == 0) selected @endif>На модерации</option>
    <option value="1" @if($item->moderated == 1) selected @endif>Одобрено</option>
    <option value="-1" @if($item->moderated == -1) selected @endif>В черном списке</option>
</select>
</div>
</div>


</div>
<!----   /country  ----> 
        
<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.created_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
{{$item->created_at}}
</div>


</div>
<!----   /country  ---->

<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.updated_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>
    <div class="box-body">
    {{$item->updated_at}}
    </div>
</div>
<!----   /country  ----> 
      

@if($item->deleted_at != null)
<!----   country  ---->
<div class="box box-danger" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">Удалено</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>
    <div class="box-body">
    {{$item->deleted_at}}
    </div>
</div>
<!----   /country  ----> 
@endif

      
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                @if($item->deleted_at != null)
                    <input type="submit" class="btn btn-success" name="restore" value="Восстановить"> 
                @else
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_modal">
                        {{ __('country.delete') }}
                    </button>
                    <input type="submit" class="btn btn-success" name="submit" value="{{ __('country.save') }}"> 
                @endif

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
      <h5 class="modal-title" id="delete_confirmation">{{ __('country.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('country.delete') }}">
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