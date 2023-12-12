
<div class="form-group col-md-3">
    <label>Select Store:*</label>
    <select class="form-control" name="store_id" id="store_id" autofocus>
        <option value="All">All Store</option>
        @if(count($stores))
            @foreach($stores as $store)
                <option value="{{$store->id}}" {{ @$store_id == $store->id ? 'selected' : '' }}>{{$store->name}}</option>
            @endforeach
        @endif
    </select>
</div>

