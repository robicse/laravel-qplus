
<div class="form-group col-md-3">
    <label>Select Customer:*</label>
    <select class="form-control select2" name="customer_id" id="customer_id" autofocus>
        <option value="All">All Customer</option>
        @if(count($customers))
            @foreach($customers as $customer)
                <option value="{{$customer->id}}" {{ @$customer_id == $customer->id ? 'selected' : '' }}>{{$customer->name}}</option>
            @endforeach
        @endif
    </select>
</div>

