<div class="form-group col-md-3">
    <label for="password" class="form-control-label">Password * </label>
    {!! Form::text('password', null, ['id' => 'password', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('password'))
        <span class="text-danger alert">{{ $errors->first('password') }}</span>
    @endif
</div>
