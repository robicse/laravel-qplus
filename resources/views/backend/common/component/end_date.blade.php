<div class="form-group col-md-3">
    <label for="end_date" class="form-control-label">End Date * </label>
    {!! Form::date('end_date', date('Y-m-d'), ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
    @if ($errors->has('end_date'))
        <span class="text-danger alert">{{ $errors->first('end_date') }}</span>
    @endif
</div>
