<div class="form-group col-md-3">
    <label for="status" class="form-control-label">Status * </label>
    {!! Form::select('status', [1 => 'Active', 0 => 'In-Active'], $status, ['id' => 'status', 'class' => 'form-control', 'required','placeholder' => 'Select One']) !!}
    @if ($errors->has('start_date'))
        <span class="text-danger alert">{{ $errors->first('start_date') }}</span>
    @endif
</div>
