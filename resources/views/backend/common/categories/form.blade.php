<div class="row">
    @include('backend.common.component.name')
</div>
@if($category)
@include('backend.common.component.update')
@else
@include('backend.common.component.submit')
@endif
