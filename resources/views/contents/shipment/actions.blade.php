@can('shipment-receive')
@if($canReceive)
<div class="px-3 pb-3 pt-1">
    <button class="btn btn-success rounded-3 w-100" data-bs-toggle="modal" data-bs-target="#collectModal">
        <i class="fas fa-box-open me-2"></i> Receive
    </button>
</div>
@endif
@endcan

@can('shipment-collect')
@if($canCollect)
<div class="px-3 pb-3 pt-1">
    <button class="btn btn-secondary rounded-3 w-100" data-bs-toggle="modal" data-bs-target="#collectModal">
        <i class="fas fa-layer-group me-2"></i> Collect
    </button>
</div>
@endif
@endcan

@can('shipment-send')
@if($canSend)
<div class="px-3 pb-3 pt-1">
    <button class="btn btn-warning text-white rounded-3 w-100" data-bs-toggle="modal" data-bs-target="#sendModal">
        <i class="fas fa-shipping-fast me-2"></i> Send
    </button>
</div>
@endif
@endcan