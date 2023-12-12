<div class="form-group col-md-3">
    <label for="unit_id">Unit <span class="required">*</span></label>
    {{-- <a type="button" class="test btn btn-primary btn-sm" onclick="modal_category()" data-toggle="modal"><i class="fa fa-plus"></i></a> --}}
    {!! Form::select('unit_id', $units, null, [
        'required',
        'id' => 'unit_id',
        'class' => 'form-control',
        'placeholder' => 'Select One',
    ]) !!}
</div>
