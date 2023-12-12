<div class="form-group col-md-3">
    <label for="website" class="form-control-label">Website * </label>
    {!! Form::text('website', null, ['id' => 'website', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('website'))
        <span class="text-danger alert">{{ $errors->first('website') }}</span>
    @endif
</div>
