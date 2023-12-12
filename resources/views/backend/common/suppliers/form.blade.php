<?php
$email_required = '';
?>
<div class="row">
    @include('backend.common.component.name')
    @include('backend.common.component.phone')
    @include('backend.common.component.email',['required'=>$email_required])
    @include('backend.common.component.start_date')
    @include('backend.common.component.opening_balance')
    <div class="form-group col-md-3">
        <label>Select Store:*</label>
        <select class="form-control" name="store_id" id="store_id" autofocus >
            <option value="">All Store</option>
            @if(count($stores))
                @foreach($stores as $store)
                    <option value="{{$store->id}}" {{ @$store_id == $store->id ? 'selected' : '' }}>{{$store->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
    @include('backend.common.component.address')
</div>
@if($supplier)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif
