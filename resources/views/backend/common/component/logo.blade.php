<div class="form-group col-md-3">
    <label for="example-text-input" class="form-control-label">Logo *</label>
    @if(@isset($store))
        <span> <img src="{{asset($store->logo)}}" alt=""  height="30px" width="30px"></span>
        @endif
    <input class="form-control" type="file" name="logo" accept="image/png, image/jpeg,image/webp">
    @if ($errors->has('logo'))
        <span class="text-danger alert">{{ $errors->first('logo') }}</span>
    @endif
</div>
