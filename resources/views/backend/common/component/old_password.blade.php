<div class="form-group col-md-3">
    <label for="oldpassword" class="form-control-label">Old Password * </label>
    {!! Form::text('oldpassword', null, ['id' => 'oldpassword', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('oldpassword'))
        <span class="text-danger alert">{{ $errors->first('oldpassword') }}</span>
    @endif
</div>
