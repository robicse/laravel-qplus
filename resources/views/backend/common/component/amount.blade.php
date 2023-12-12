<div class="form-group col-md-3">
    <label for="amount" class="form-control-label">Amount * </label>
    {!! Form::number('amount', null, ['id' => 'amount', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('amount'))
        <span class="text-danger alert">{{ $errors->first('amount') }}</span>
    @endif
</div>
