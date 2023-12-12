
<div class="form-group col-md-3">
    <label>Select Supplier:*</label>
    <select class="form-control select2" name="supplier_id" id="supplier_id" autofocus>
        <option value="All">All Supplier</option>
        @if(count($suppliers))
            @foreach($suppliers as $supplier)
                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
            @endforeach
        @endif
    </select>
</div>

