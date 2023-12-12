<div class="form-group col-md-3">
    <label for="name" class="form-control-label">Name * </label>
    {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('name'))
        <span class="text-danger alert">{{ $errors->first('name') }}</span>
    @endif
</div>
