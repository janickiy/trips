<!---- select field: {{$param['name']}} ---->

<div class="form-group">
    <label class="form-label">@isset($param['label']){{$param['label']}}@endisset{{''}}@isset($param['required']){{''}}@if($param['required'] == true) <span class="text-danger" title="required">*</span>@endif{{''}}@endisset</label>
    @if(@isset($param['selected']))
        @if(@isset($param['old_selected']))
            <?php $selected = $param['old_selected']; ?>
        @else
            <?php $selected = $param['selected']; ?>
        @endif
    @else
         <?php $selected = null; ?>
    @endif
    @if(@isset($param['values']))

    <select{{''}}@isset($param['name']) name="{{$param['name']}}"@endisset{{''}}@isset($param['id']) id="{{$param['id']}}"{{''}}@endisset{{''}}@isset($param['class']) class="{{$param['class']}}"{{''}}@endisset{{''}}@isset($param['required']){{''}}@if($param['required'] == true){{''}} required{{''}}@endif{{''}}@endisset{{''}}@isset($param['readonly']){{''}}@if($param['readonly'] == true){{''}} readonly{{''}}@endif{{''}}@endisset{{''}}>
    @forelse($param['values'] as $value => $text)
    <option value="{{$value}}" @if($selected !== null){{''}}@if($selected == $value){{''}}selected{{''}}@endif{{''}}@endif{{''}}>{{$text}}</option>
    @empty
    @endforelse
</select>

    @endif
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
<!---- /select field: {{$param['name']}} ---->
