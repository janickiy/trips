<!---- text field: {{$param['name']}} ---->

<div class="form-group">
    <label class="form-label">@isset($param['label']){{$param['label']}}@endisset{{' '}}@isset($param['required']){{''}}@if($param['required'] == true)<span class="text-danger" title="required">*</span>@endif{{''}}@endisset </label>

    <input type="text"{{''}}@isset($param['name']) name="{{$param['name']}}"{{''}}@endisset{{''}}@isset($param['placeholder']) placeholder="{{$param['placeholder']}}"{{''}}@endisset{{''}}@isset($param['value']) value="{{$param['old_value'] ? $param['old_value'] : $param['value']}}"{{''}}@endisset{{''}}@isset($param['id']) id="{{$param['id']}}"{{''}}@endisset{{''}}@isset($param['class']) class="{{$param['class']}}"{{''}}@endisset{{''}}@isset($param['minlength']) minlength="{{$param['minlength']}}"{{''}}@endisset{{''}}@isset($param['maxlength']) maxlength="{{$param['maxlength']}}"{{''}}@endisset{{''}}@isset($param['autocomplete']) autocomplete="{{$param['autocomplete']}}"{{''}}@endisset{{''}}@isset($param['required']){{''}}@if($param['required'] == true){{' '}}required pattern=".*\S+.*"{{''}}@endif{{''}}@endisset{{''}}@isset($param['readonly']){{''}}@if($param['readonly'] == true){{''}} readonly{{''}}@endif{{''}}@endisset{{''}}>

    <small id="{{$param['name']}}_js_error" class="form-text text-danger" style="display: none;"></small>

</div>

@isset($param['errors'])
<div class="form-group">
    <div class="alert alert-warning" role="alert">
        @if(is_array($param['errors']) and $param['errors'] != null)
            @forelse($param['errors'] as $id => $error)
                {{$error}} <br>
            @empty
            @endforelse
        @endif
    </div>
</div>
@endisset
<!---- /text field: {{$param['name']}} ---->
