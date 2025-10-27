@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        {{-- ==== CARD SHIPMENT INFO ==== --}}
        <div class="col-md-12">
            <div class="row">
                {{-- === LEFT SIDE === --}}
                <div class="col-md-6">
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
                                        <i class="fas fa-truck me-1"></i> Shipment Number:
                                        <span class="fw-bolder">{{ strtoupper($shipment->no_shipment) }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-calendar-alt me-2"></i> Created At:
                                        <span class="fw-bolder">{{ $shipment->created_at->format('d-M-Y H:i:s') }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-shapes me-2"></i> Category:
                                        <span class="fw-bolder">{{ strtoupper($shipment->category->name) }}</span>
                                    </h5>
                                    <h5 class="shipment-store-card">
                                        <i class="fas fa-quote-right me-2"></i> Notes:
                                        <span class="fw-bolder">{{ strtoupper($shipment->note ?? '-') }}</span>
                                    </h5>
                                </div>
                                <div class="col-3 col-md-2 ps-0 text-end">
                                    <img class="w-100 cursor-pointer" id="qrThumbnail"
                                         src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                                    <div class="text-center mt-2">
                                        <small class="text-muted">Scan QR Code</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- === RIGHT SIDE === --}}
                <div class="col-md-6">
                    <div class="card card-warning bg-warning-gradient">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="fw-bolder"><i class="fas fa-ellipsis-v me-2"></i> Destination</h5>
                                </div>
                                <div class="col-4 ps-0 text-end">
                                    <a href="#" class="text-light" data-bs-toggle="modal" data-bs-target="#trackingModal">
                                        <i class="fas fa-route me-1"></i> Track Shipment
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bubble-shadow">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-0 op-8">
                                        <i class="fas fa-dot-circle me-2"></i>
                                        {{ ucwords(optional($shipment->sender_location)->name ?? '-') }}
                                    </h5>
                                    <div class="op-7">
                                        {{ ucwords($shipment->sender_pic ?? '-') }}
                                    </div>

                                    <hr>

                                    <h5 class="fw-bolder mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ ucwords(optional($shipment->receiver_location)->name ?? '-') }}
                                    </h5>
                                    <div class="fw-bold op-8">
                                        {{ ucwords($shipment->destination_pic ?? '-') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==== ITEM DETAILS ==== --}}
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
                                        <i class="fas fa-box me-3"></i> Item Details
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
                            <div class="row mt-3">
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

                                    <div class="col-12 col-md-6 col-xxl-4 px-4 mb-3">
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
                                                        {{ strtoupper($detail->label ?? '-') }} |
                                                        {{ number_format($detail->quantity, 0) }} {{ strtoupper($detail->uom) }}
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
                        @if($shipment->status == '1' || $shipment->status == '2' || $shipment->status == '3')
                        <div class="card-footer">
                            @if($shipment->status == '1' || $shipment->status == '3')
                            <div class="d-flex justify-content-between align-items-center p-2">
                                <!-- Info di sisi kiri -->
                                <div class="text-muted small">
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                    Re-check shipment before <strong>Collect</strong>.
                                </div>

                                <!-- Tombol buka modal -->
                                <button type="button" class="btn btn-label-secondary btn-round" data-bs-toggle="modal" data-bs-target="#collectModal">
                                    <i class="fas fa-database me-1"></i> Collect
                                </button>
                            </div>
                            @endif

                            @if($shipment->status == '2')
                            <div class="d-flex justify-content-between align-items-center p-2">
                                <!-- Notes di sisi kiri -->
                                <div class="text-muted small">
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                    Re-check shipment before <strong>Send</strong>.
                                </div>

                                <!-- Tombol submit di sisi kanan -->
                                <form action="{{ route('shipments.collect', ['noShipment' => encrypt($shipment->no_shipment)]) }}" method="POST">
                                    @csrf
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-label-primary btn-round">
                                            <i class="fab fa-telegram-plane me-1"></i> Send
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==== MODAL QR CODE ==== --}}
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body">
                    <img class="w-75" src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                </div>
            </div>
        </div>
    </div>

    {{-- ==== MODAL TRACKING ==== --}}
    <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bolder"><i class="fas fa-route me-2"></i> Shipment Tracking | <span class="fw-normal fs-6">{{ $shipment->no_shipment }}</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="timeline position-relative pt-3 ps-2">
                        @forelse($shipment->shipment_ledger as $index => $log)
                            <div class="timeline-row d-flex position-relative">
                                
                                {{-- Garis penghubung antar item --}}
                                @if(!$loop->last)
                                    <div class="timeline-connector"></div>
                                @endif

                                <div class="timeline-item d-flex align-items-start mb-4 w-100">
                                    
                                    {{-- Kiri: Tanggal --}}
                                    <div class="timeline-date text-muted small text-end pe-3">
                                        {{ $log->created_at ? $log->created_at->format('d M Y H:i') : '-' }}
                                    </div>

                                    {{-- Tengah: Icon dalam lingkaran --}}
                                    <div class="timeline-icon-wrapper">
                                        @php
                                            $icon = 'fa-circle text-secondary';
                                            switch ($log->status) {
                                                case '1': $icon = 'fa-box text-info'; break;
                                                case '2': $icon = 'fa-truck-loading text-secondary'; break;
                                                case '3': $icon = 'fa-shipping-fast text-warning'; break;
                                                case '4': $icon = 'fa-people-carry text-primary'; break;
                                                case '5': $icon = 'fa-box-open text-success'; break;
                                                case '6': $icon = 'fa-times text-danger'; break;
                                            }
                                        @endphp
                                        <div class="timeline-icon-circle">
                                            <i class="fas {{ $icon }}"></i>
                                        </div>
                                    </div>

                                    {{-- Kanan: Deskripsi --}}
                                    <div class="timeline-content ps-3">
                                        <h6 class="fw-bolder mb-0 text-dark">{{ ucwords($log->description ?? '-') }}</h6>
                                        <p class="mt-0 mb-0 text-muted">{{ ucfirst($log->note) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">No tracking history available</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Collect -->
    <div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="collectModalLabel">
                        <i class="fas fa-database me-2"></i>Confirm Collect
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('shipments.collect', ['noShipment' => encrypt($shipment->no_shipment)]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Add any notes before collecting... (optional)"></textarea>
                        </div>
                        {{-- <p class="text-muted small mb-0">
                            Please make sure all shipment details are correct before proceeding.
                        </p> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-label-secondary btn-round">
                            <i class="fas fa-database me-1"></i> Collect
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('customScripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qrThumbnail = document.getElementById("qrThumbnail");
        qrThumbnail.addEventListener("click", () => {
            const qrModal = new bootstrap.Modal(document.getElementById("qrModal"));
            qrModal.show();
        });
    });
</script>
@endsection
