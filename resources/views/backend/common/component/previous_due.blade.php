<div class="form-group col-md-3">
    <label for="previous_due" class="form-control-label">Previous Due * </label>
    {!! Form::number('previous_due', null, ['id' => 'previous_due', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('previous_due'))
        <span class="text-danger alert">{{ $errors->first('previous_due') }}</span>
    @endif
</div>
