<div class="form-group col-md-3">
    <label for="confirm-password" class="form-control-label">Confirm Password * </label>
    {!! Form::text('confirm-password', null, ['id' => 'confirm-password', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('confirm-password'))
        <span class="text-danger alert">{{ $errors->first('confirm-password') }}</span>
    @endif
</div>
