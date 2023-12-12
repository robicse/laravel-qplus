<?php
$email_required = '';
$status = '1';
?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="purchase_date">Purchase Date <span class="required">*</span></label>
            {!! Form::date('purchase_date', date('Y-m-d'), ['id' => 'purchase_date', 'class' => 'form-control', 'required', 'tabindex' => 4]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Store <span class="required">*</span></label>
            {!! Form::select('store_id', @$stores, @$purchase->store_id, [
                'id' => 'store_id',
                'class' => 'form-control select2',
                'placeholder' => 'Select One',
                'required',
                'autofocus'
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Supplier <span class="required">*</span></label>
            {!! Form::select('supplier_id', @$suppliers, @$purchase->supplier_id, [
                'id' => 'supplier_id',
                'class' => 'form-control select2',
                'placeholder' => 'Select One',
                'required'
            ]) !!}
        </div>
    </div>
    {{-- @include('backend.common.component.amount') --}}
    {{-- @include('backend.common.component.status') --}}
</div>
{{-- @if($package)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif --}}
