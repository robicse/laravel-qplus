<?php
$email_required = '';
?>
<div class="row">
    @include('backend.common.component.category')
    @include('backend.common.component.unit')
    @include('backend.common.component.name')
    @include('backend.common.component.stock_low_qty')
</div>
@if($product)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif
