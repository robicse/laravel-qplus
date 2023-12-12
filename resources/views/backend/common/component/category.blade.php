<div class="form-group col-md-3">
    <label for="category_id">Category <span class="required">*</span></label>
    {{-- <a type="button" class="test btn btn-primary btn-sm" onclick="modal_category()" data-toggle="modal"><i class="fa fa-plus"></i></a> --}}
    {!! Form::select('category_id', $categories, null, [
        'required',
        'id' => 'category_id',
        'class' => 'form-control',
        'placeholder' => 'Select One',
    ]) !!}
</div>
