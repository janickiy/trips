@extends('cp.cp_tk')

@section('content_title') {{ __('wikidata_country.edit_wikidata_country') }} #{{$item->id}} @endsection

@section('content_breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{URL::to("/")}}/cp">{{ __('cp.control_panel') }}</a></li>
    <li><a href="{{URL::to("/")}}/cp/wikidata_countries" id="items_list">{{ __('wikidata_country.wikidata_country') }}</a></li>
    <li class="active">{{ __('wikidata_country.edit_wikidata_country') }} #{{$item->id}}:</li>
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

<form method="post" action="{{URL::to('/')}}/cp/wikidata_country/edit" autocomplete="off" enctype="multipart/form-data">
{{ csrf_field() }}


<!---- CONTENT: ---->
<div class="row">

    <div class="col-md-8">

<!---- A_I --->
    <input type="hidden" name="id" value="{{$item->id}}" id="column_id">
<!---- A_I --->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.wiki_id') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="wiki_id" value="{{$item->wiki_id}}" placeholder="{{__('wikidata_country.wiki_id')}}" class="form-control input-sm" id="column_wiki_id"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->

<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.wiki_link') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="wiki_link" value="{{$item->wiki_link}}" placeholder="{{__('wikidata_country.wiki_link')}}" class="form-control input-sm" id="column_wiki_link"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->

<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.name_ru') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="name_ru" value="{{$item->name_ru}}" placeholder="{{__('wikidata_country.name_ru')}}" class="form-control input-sm" id="column_name_ru"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.name_en') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="name_en" value="{{$item->name_en}}" placeholder="{{__('wikidata_country.name_en')}}" class="form-control input-sm" id="column_name_en"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->



<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.code') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="code" value="{{$item->code}}" placeholder="{{__('wikidata_country.code')}}" class="form-control input-sm" id="column_code"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->


<!----   wikidata_country  ---->
<div class="box box-success" style="position: relative; left: 0px; top: 0px;">
<div class="box-header ui-sortable-handle">

    <!--- Essense:  --->
    <h3 class="box-title">{{ __('wikidata_country.updated_at') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
        </button>
    </div>

</div>

<div class="box-body">
<div class="form-group">

<input type="text" name="updated_at" value="{{$item->updated_at}}" placeholder="{{__('wikidata_country.updated_at')}}" class="form-control input-sm" id="column_updated_at"  disabled>

                                </div>
</div>


</div>
<!----   /wikidata_country  ---->

    </div>

    <div class="col-md-4">

        <!----   wikidata_city  ---->
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
        <div class="box-header ui-sortable-handle">

            <!--- Essense:  --->
            <h3 class="box-title">TODO:</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

        </div>

        <div class="box-body">
        <div class="form-group">
UPDATE
        </div>


        </div>
        </div>
        <!----   /wikidata_city  ---->


        <!----   wikidata_city  ---->
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
        <div class="box-header ui-sortable-handle">

            <!--- Essense:  --->
            <h3 class="box-title">{{ __('wikidata_city.region') }}</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

        </div>

        <div class="box-body">
        <div class="form-group">

        <input type="hidden" name="region" value="{{$item->region}}" id="column_region">

                                        </div>
        </div>


        </div>
        <!----   /wikidata_city  ---->
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
                    {{ __('wikidata_country.delete') }}
                </button>
                <input type="submit" class="btn btn-success" name="submit" value="{{ __('wikidata_country.save') }}">
            </div>
        </div>
    </div>
</div>
S -->
<!-- /ACTION BUTTONS -->


<!-- Modal -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_confirmation" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body">
      <h5 class="modal-title" id="delete_confirmation">{{ __('wikidata_country.delete_confirmation') }}</h5>
      </div>
      <div class="modal-footer">
        <input type="submit" name="delete" class="btn btn-danger" value="{{ __('wikidata_country.delete') }}">
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
