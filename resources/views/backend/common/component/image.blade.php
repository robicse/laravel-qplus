<div class="form-group col-md-3">
    <label for="example-text-input" class="form-control-label">Image *</label>
    <input class="form-control" type="file" name="image" accept="image/png, image/jpeg,image/webp">
    @if ($errors->has('image'))
        <span class="text-danger alert">{{ $errors->first('image') }}</span>
    @endif
</div>
