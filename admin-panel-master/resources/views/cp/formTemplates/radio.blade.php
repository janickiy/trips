<!---- select field: {{$param['name']}} ---->

<div class="form-group">
    <label class="form-label">@isset($param['label']){{$param['label']}}@endisset{{''}}@isset($param['required']){{''}}@if($param['required'] == true) <span class="text-danger" title="required">*</span>@endif{{''}}@endisset</label>
    @if(@isset($param['checked']))
        @if(@isset($param['old_checked']))
            <?php $selected = $param['old_checked']; ?>
        @else
            <?php $selected = $param['checked']; ?>
        @endif
    @else
         <?php $selected = null; ?>
    @endif

    @if(@isset($param['values']))
        @forelse($param['values'] as $value => $text)
    <div><label><input type="radio"{{''}}@isset($param['name']) name="{{$param['name']}}"@endisset{{''}}@isset($param['id']) id="{{$param['id']}}"@endisset{{''}}@isset($param['class']) class="{{$param['class']}}"{{''}}@endisset{{''}}@isset($param['required']){{''}}@if($param['required'] == true){{''}} required{{''}}@endif{{''}}@endisset{{''}}@isset($param['readonly']){{''}}@if($param['readonly'] == true){{''}} readonly{{''}}@endif{{''}}@endisset{{''}} value="{{$value}}"{{''}}@if($selected !== null){{''}}@if($selected == $value){{''}} checked{{''}}@endif{{''}}@endif{{''}}> {{$text}}</label></div>
        @empty
        @endforelse
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
