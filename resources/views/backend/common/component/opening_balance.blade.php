<div class="form-group col-md-3">
    <label for="opening_balance" class="form-control-label">Previous Due * </label>
    {!! Form::number('opening_balance', null, ['id' => 'opening_balance', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('previous_due'))
        <span class="text-danger alert">{{ $errors->first('opening_balance') }}</span>
    @endif
</div>
