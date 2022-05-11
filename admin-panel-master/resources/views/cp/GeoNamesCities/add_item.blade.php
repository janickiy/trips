@extends('cp.cp_tk')

@section('content_title') {{ __('country.create_country') }} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/country" id="items_list">{{ __('country.country') }}</a></li>
    <li class="active">{{ __('country.create_country') }}:</li>
</ol>
@endsection

@section('content')

<form method="post" action="{{URL::to('/')}}/cp/country/add" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}



<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">
        
<!----   country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('country.name') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>
    
</div>

<div class="box-body">
<div class="form-group">
<input type="text" name="name_ru" value="" placeholder="{{__('country.name')}}" class="form-control input-sm" id="column_name_ru" required>
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
<input type="text" name="wiki_id" value="" placeholder="{{__('country.wiki_id')}}" class="form-control input-sm" id="column_wiki_id" required >
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
<input type="text" name="wiki_link" value="" placeholder="{{__('country.wiki_link')}}" class="form-control input-sm" id="column_wiki_link" required >
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
    <option value="0">На модерации</option>
    <option value="1">Одобрено</option>
    <option value="-1">В черном списке</option>
</select>
</div>
</div>


</div>
<!----   /country  ---->     
        
    </div>
    
</div>
<!---- /CONTENT ---->

<!-- ACTION BUTTONS -->
<div class="row">
    <div class="col-md-12 text-center">
        <div class="box box-success">
            <div class="box-header">
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('country.create') }}">
            </div>
        </div>
    </div>
</div>
<!-- /ACTION BUTTONS -->

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

// TODO: create by button
</script>
@endsection