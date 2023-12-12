<div class="form-group col-md-3">
    <label for="location" class="form-control-label">Location * </label>
    {!! Form::text('location', null, ['id' => 'location', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('location'))
        <span class="text-danger alert">{{ $errors->first('location') }}</span>
    @endif
</div>
