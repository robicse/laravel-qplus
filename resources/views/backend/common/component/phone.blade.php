<div class="form-group col-md-3">
    <label for="phone" class="form-control-label">Phone * </label>
    {!! Form::number('phone', null, ['id' => 'phone', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('phone'))
        <span class="text-danger alert">{{ $errors->first('phone') }}</span>
    @endif
</div>
