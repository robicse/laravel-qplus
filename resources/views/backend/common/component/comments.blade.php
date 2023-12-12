<div class="form-group col-md-3">
    <label for="comments" class="form-control-label">Comments  </label>
    {!! Form::text('comments', null, ['id' => 'address', 'class' => 'form-control']) !!}
    @if ($errors->has('comments'))
        <span class="text-danger alert">{{ $errors->first('comments') }}</span>
    @endif
</div>
