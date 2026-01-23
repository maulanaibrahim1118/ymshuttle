@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-stats card-round">
                        <div class="card-header">
                            <h5 class="card-title">
                                <div class="row">
                                    <div class="col-7">
                                        <a href="javascript:history.back()">
                                            <i class="fas fa-arrow-left"></i>
                                        </a>
                                        <span class="ms-4">{{ $title }}</span>
                                    </div>
                                    <div class="col-5 text-end">
                                        <h5 class="badge {{ $shipment->badge['class'] }} mt-1">
                                            <i class="{{ $shipment->badge['icon'] }} me-1"></i>
                                            {{ $shipment->badge['label'] }}
                                        </h5>
                                    </div>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-9 col-md-10 px-4">
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-box me-1"></i> No. Shipment:
                                        <span class="fw-bolder">{{ strtoupper($shipment->no_shipment) }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-calendar-alt me-2"></i> Created At:
                                        <span class="fw-bolder">{{ strtoupper($shipment->created_at->format('d-M-Y H:i:s')) }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-shapes me-2"></i> Category:
                                        <span class="fw-bolder">{{ strtoupper($shipment->category->name) }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-quote-right me-2"></i> Description:
                                        <span class="fw-bolder">{{ strtoupper($shipment->notes ?? '-') }}</span>
                                    </h5>
                                </div>
                                <div class="col-3 col-md-2 ps-0 text-end">
                                    <img class="w-100 cursor-pointer" id="qrThumbnail" src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                                    <div class="text-center mt-2 d-flex gap-1">
                                        <a href="{{ route('shipments.copy', $shipment->no_shipment) }}"
                                            class="btn btn-sm btn-label-primary btn-round fw-bold w-50" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <a href="{{ route('shipments.print', $shipment->no_shipment) }}"
                                            target="_blank"
                                            onclick="openPrintPage(event, '{{ route('shipments.print', $shipment->no_shipment) }}')"
                                            class="btn btn-sm btn-label-success btn-round fw-bold w-50" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @can('shipment-delete')
                            @if($canDelete)
                            <div class="pb-2 mt-3 pt-3 d-flex gap-2 border-top border-2">
                                <a href="{{ route('shipments.edit', ['id' => encrypt($shipment->id)]) }}" class="btn btn-label-info rounded-3 w-50">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </a>
                                <button class="btn btn-label-danger rounded-3 w-50" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                </button>
                            </div>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card card-warning bg-warning-gradient">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="fw-bolder"><i class="fas fa-plane me-2"></i> Shipment Route <span class="fw-normal fs-6"><i>[{{ $shipment->shipment_by_label }}]</i></span></h5>
                                </div>
                                <div class="col-4 ps-0 text-end">
                                    <a href="#" class="text-light" data-bs-toggle="modal" data-bs-target="#trackingModal">
                                        <i class="fas fa-route me-2"></i>Track
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bubble-shadow">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <h6 class="fw-normal text-uppercase mb-0 op-8">
                                        <i class="fas fa-dot-circle me-1"></i>
                                        {{ ucwords(optional($shipment->sender_location)->clean_name ?? '-') }}
                                    </h6>
                                    <div class="fs-7 op-7">
                                        <i>{{ ucwords($shipment->sender_pic ?? '-') }}</i>
                                    </div>

                                    <hr>

                                    <h6 class="fw-bolder text-uppercase mb-0">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ optional($shipment->receiver_location)->clean_name ?? '-' }}
                                    </h6>
                                    <div class="fw-bold op-8">
                                        <i>{{ ucwords($shipment->destination_pic ?? '-') }}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-stats card-round">
                        <div class="card-body pb-0">
                            <div class="accordion accordion-flush" id="accordionExample">
                                <div class="accordion-item mx-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed p-0" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                        aria-expanded="true" aria-controls="collapseTwo">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-comment-dots me-3"></i> Shipping Notes
                                        </h5>
                                    </button>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="border-top"></div>
                    <div id="collapseTwo" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="card-body">
                                @if($shippingNotes->isNotEmpty())
                                <div class="row px-4">
                                    @foreach($shippingNotes as $data)
                                    <h6 class="fw-bolder mb-0">{{ ucwords($data->creator->name) }} <span class="text-muted fw-normal ms-1" style="font-size: 12px;">{{ $data->created_at->format('d-m-Y H:i') }}</span></h6>
                                    <p><i class="fas fa-caret-right text-warning me-2"></i>{{ $data->notes }}</i></p>
                                    @endforeach
                                </div>
                                @else
                                <div class="row text-center px-4">
                                    <p class="text-muted">No notes available.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-stats card-round">
                        <div class="card-body pb-0">
                            <div class="accordion accordion-flush" id="accordionExample">
                                <div class="accordion-item mx-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed p-0" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="true" aria-controls="collapseOne">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-boxes me-3"></i> Item Details
                                            </h5>
                                        </button>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="border-top"></div>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="card-body">
                                    <div class="row mt-2">
                                        @forelse ($shipment->shipment_detail as $index => $detail)
                                            @php
                                                $condition = strtolower($detail->condition);
                                                switch ($condition) {
                                                    case 'good':
                                                        $badgeClass = 'success';
                                                        $badgeIcon = 'fa-check-circle';
                                                        break;
                                                    case 'broken':
                                                        $badgeClass = 'danger';
                                                        $badgeIcon = 'fa-times-circle';
                                                        break;
                                                    default:
                                                        $badgeClass = 'secondary';
                                                        $badgeIcon = 'fa-box';
                                                        break;
                                                }
                                            @endphp

                                            <div class="col-12 col-md-12 mb-3">
                                                <div class="card alert-warning mb-0 shadow-sm position-relative shipment-item-card">
                                                    <div class="card-body d-flex justify-content-between align-items-center">
                                                        
                                                        <!-- Left: Nomor urut -->
                                                        <div class="shipment-item-index">
                                                            <div class="shipment-item-number">
                                                                {{ $index + 1 }}
                                                            </div>
                                                        </div>

                                                        <!-- Middle: Informasi utama -->
                                                        <div class="shipment-item-info">
                                                            <h7 class="text-uppercase fw-bolder text-truncate mb-0">
                                                                {{ $detail->item_name }}
                                                            </h7>
                                                            <h7 class="text-muted mb-0 text-truncate">
                                                                {{ rtrim(rtrim(number_format($detail->quantity, 2, '.', ''), '0'), '.') }} {{ strtoupper($detail->uom) }}
                                                                {{ $detail->label ? '| '.strtoupper($detail->label) : '' }}
                                                            </h7>
                                                        </div>

                                                        <!-- Right: Condition -->
                                                        <div class="shipment-item-condition">
                                                            <span class="badge bg-{{ $badgeClass }} px-2 py-1">
                                                                <i class="fas {{ $badgeIcon }} me-1"></i>{{ ucfirst($condition) }}
                                                            </span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center text-muted py-3">
                                                No shipment items found.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('contents.shipment.actions')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('contents.shipment.modals')
@endsection

@section('customScripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const qrThumbnail = document.getElementById("qrThumbnail");
    if (qrThumbnail) {
        qrThumbnail.addEventListener("click", () => {
            const qrModal = new bootstrap.Modal(document.getElementById("qrModal"));
            qrModal.show();
        });
    }

    // const sendBtn = document.getElementById("sendBtn");
    // if (sendBtn) {
    //     sendBtn.addEventListener("click", async function () {
    //         sendBtn.disabled = true;
    //         sendBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Sending...`;

    //         try {
    //             const response = await fetch("{{ route('shipments.send', ['noShipment' => encrypt($shipment->no_shipment)]) }}", {
    //                 method: "POST",
    //                 headers: {
    //                     "Content-Type": "application/json",
    //                     "X-CSRF-TOKEN": "{{ csrf_token() }}"
    //                 },
    //                 body: JSON.stringify({})
    //             });

    //             const data = await response.json();

    //             if (data.success) {
    //                 swal("Success!", "Shipment has been send.", "success", {
    //                     timer: 1500,
    //                 });
    //                 setTimeout(() => {
    //                     window.location.reload();
    //                 }, 1500);
    //             } else {
    //                 swal("Warning!", "Failed to send shipment.", "warning", {
    //                     timer: 1500,
    //                 });
    //             }
    //         } catch (error) {
    //             swal("Warning!", "Failed to send data to the server.", "warning", {
    //                 timer: 1500,
    //             });
    //         } finally {
    //             sendBtn.disabled = false;
    //             sendBtn.innerHTML = `<i class="fab fa-telegram-plane me-1"></i> Send`;
    //         }
    //     });
    // }

    const confirmPrintBtn = document.getElementById('confirmPrintBtn');
    if (confirmPrintBtn) {
        confirmPrintBtn.addEventListener('click', function () {
            const copies = parseInt(document.getElementById('copiesInput').value);
            if (!copies || isNaN(copies) || copies < 1) return;

            const url = `${printBaseUrl}?copies=${copies}`;
            window.open(url, '_blank', 'noopener');

            const modalEl = document.getElementById('copiesModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();
        });
    }
});

let printBaseUrl = '';
function openPrintPage(event, baseUrl) {
    event.preventDefault();
    printBaseUrl = baseUrl;

    const modal = new bootstrap.Modal(document.getElementById('copiesModal'));
    modal.show();
}

function validateDeliveryImage() {
    const input = document.querySelector('input[name="delivery_image"]');

    if (input && input.hasAttribute('required')) {
        if (!input.checkValidity()) {
            $.notify({
                icon: 'icon-bell',
                title: 'Warning!',
                message: 'Please capture an image.',
            },{
                type: 'warning',
                placement: {
                    from: "top",
                    align: "right"
                },
                delay: 2000,
                z_index: 2000
            });
            return false; // STOP submit
        }
    }

    return true; // lanjut submit
}
</script>

@role('messenger')
<script>
function previewSendImage(event) {
    const img = document.getElementById('send-image-preview');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>
@endrole
@endsection
