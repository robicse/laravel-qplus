<?php
$email_required = 1;
?>
<div class="row">
    @include('backend.common.component.name')
    @include('backend.common.component.location')
    @include('backend.common.component.phone')
    @include('backend.common.component.email',['required'=>$email_required])
    {{-- @include('backend.common.component.website')
    @include('backend.common.component.logo') --}}
    @include('backend.common.component.address')
</div>
