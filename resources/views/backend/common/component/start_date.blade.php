<div class="form-group col-md-3">
    <label for="start_date" class="form-control-label">Start Date * </label>
    {!! Form::date('start_date', null, ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
    @if ($errors->has('start_date'))
        <span class="text-danger alert">{{ $errors->first('start_date') }}</span>
    @endif
</div>

