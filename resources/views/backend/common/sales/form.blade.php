<?php
$email_required = '';
$status = '1';
?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="voucher_date">Voucher Date <span class="required">*</span></label>
            {!! Form::date('voucher_date', null, ['id' => 'voucher_date', 'class' => 'form-control', 'required', 'tabindex' => 4]) !!}
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
                'autofocus'
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
                'required'
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Package</label>
            {!! Form::select('package_id', @$packages, null, [
                'id' => 'package_id',
                'class' => 'form-control',
                'placeholder' => 'Select One'
            ]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="hc_voucher_number">Hard copy voucher number </label>
            {!! Form::text('hc_voucher_number', null, ['id' => 'hc_voucher_number', 'class' => 'form-control', 'tabindex' => 4]) !!}
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
