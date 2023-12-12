<?php
$email_required = '';
$status = '1';
?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="return_date">Return Date <span class="required">*</span></label>
            {!! Form::date('return_date', date('Y-m-d'), ['id' => 'return_date', 'class' => 'form-control', 'required', 'tabindex' => 4]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Voucher <span class="required">*</span></label>
            {!! Form::select('sale_id', @$sales, null, [
                'id' => 'sale_id',
                'class' => 'form-control select2',
                'placeholder' => 'Select One',
                'required',
                'autofocus'
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Store <span class="required">*</span></label>
            {!! Form::select('store_id', @$stores, null, [
                'id' => 'store_id',
                'class' => 'form-control select2',
                'placeholder' => 'Select One',
                'required',
                'autofocus',
                'disabled'
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Customer <span class="required">*</span></label>
            {!! Form::select('customer_id', @$customers, null, [
                'id' => 'customer_id',
                'class' => 'form-control select2',
                'placeholder' => 'Select One',
                'required',
                'disabled'
            ]) !!}
        </div>
    </div>
    @include('backend.common.component.comments')
    {{-- @include('backend.common.component.status') --}}
</div>
{{-- @if($package)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif --}}
