<div class="form-group col-md-3">
    <label for="stock_low_qty" class="form-control-label">Low Stock Qty * </label>
    {!! Form::number('stock_low_qty', null, ['id' => 'stock_low_qty', 'class' => 'form-control', 'required']) !!}
    @if ($errors->has('stock_low_qty'))
        <span class="text-danger alert">{{ $errors->first('stock_low_qty') }}</span>
    @endif
</div>
