<div class="form-group col-md-3">
    <label for="address" class="form-control-label">Address * </label>
    {!! Form::text('address', null, ['id' => 'address', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('address'))
        <span class="text-danger alert">{{ $errors->first('address') }}</span>
    @endif
</div>
