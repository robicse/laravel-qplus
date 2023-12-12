<div class="form-group col-md-3">
    <label for="email" class="form-control-label">Email {{ @$email_required != '' ? '*' : '' }} </label>
    {!! Form::email('email', null, ['id' => 'email', 'class' => 'form-control', @$email_required != '' ? 'required' : '' ]) !!}
    @if ($errors->has('email'))
        <span class="text-danger alert">{{ $errors->first('email') }}</span>
    @endif
</div>
