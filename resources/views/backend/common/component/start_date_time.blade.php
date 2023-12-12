<div class="form-group col-md-3">
    <label for="start_date" class="form-control-label">Start Date * </label>
    {!! Form::text('start_date', date('Y-m-d H:i:s'), ['id' => 'start_date', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('start_date'))
        <span class="text-danger alert">{{ $errors->first('start_date') }}</span>
    @endif
</div>
