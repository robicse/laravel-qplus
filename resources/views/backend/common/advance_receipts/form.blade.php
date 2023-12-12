<div class="row">
    @include('backend.common.component.date')
    @include('backend.common.component.store')
    @include('backend.common.component.customer')
    @include('backend.common.component.amount')
    @include('backend.common.component.payment_type')
</div>
@if($advance_receipt)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif
