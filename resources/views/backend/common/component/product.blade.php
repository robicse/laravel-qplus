
<div class="form-group col-md-3">
    <label>Select Product:*</label>
    <select class="form-control" name="product_id" id="product_id" autofocus>
        <option value="All">All Product</option>
        @if(count($products))
            @foreach($products as $product)
                <option value="{{$product->id}}">{{$product->name}}</option>
            @endforeach
        @endif
    </select>
</div>

