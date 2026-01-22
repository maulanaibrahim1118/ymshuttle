{{-- ==== MODAL QR CODE ==== --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <img class="w-75" src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                <h4 class="fw-bolder">{{ strtoupper($shipment->no_shipment) }}</h4>
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
                                            case '2': $icon = 'fa-shipping-fast text-warning'; break;
                                            case '3': $icon = 'fa-layer-group text-secondary'; break;
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
                                    <p class="mt-0 mb-0 text-muted">{{ ucfirst($log->notes) }}</p>
                                    
                                    @if(!empty($log->img_path))
                                        <a href="{{ asset('storage/' . $log->img_path) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="d-inline-block mt-1 text-decoration-none fw-semibold text-primary">
                                            <i class="fas fa-camera me-1"></i> Lihat bukti pengiriman
                                        </a>
                                    @endif
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

<!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-trash-alt me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('shipments.destroy', ['noShipment' => encrypt($shipment->no_shipment)]) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure, want to delete this shipment?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-label-danger btn-round">
                        <i class="fas fa-trash-alt me-1"></i> Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Collect/Receive -->
<div class="modal fade" id="collectModal" tabindex="-1" aria-labelledby="collectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="collectModalLabel">
                    <i class="{{ $shipment->destination == auth()->user()->location_code ? 'fas fa-box-open' : 'fas fa-layer-group' }}  me-2"></i>Confirm {{ $shipment->destination == auth()->user()->location_code ? 'Receive' : 'Collect' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('shipments.collect', ['noShipment' => encrypt($shipment->no_shipment)]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Add any notes ... (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-label-primary btn-round">
                        <i class="fab fa-telegram-plane me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Send -->
<div class="modal fade" id="sendModal" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="sendModalLabel">
                    <i class="fas fa-shipping-fast me-2"></i>Confirm Send
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('shipments.send', ['noShipment' => encrypt($shipment->no_shipment)]) }}" method="POST"  enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 {{ auth()->user()->hasRole('messenger') ? '' : 'd-none' }}">
                        <div class="image-upload border rounded text-center p-2" style="max-width: 100px;">
                            <label style="cursor:pointer;">
                                <input type="file"
                                    name="delivery_image"
                                    accept="image/jpeg,image/png"
                                    capture="camera"
                                    {{ auth()->user()->hasRole('messenger') ? 'required' : '' }}
                                    hidden
                                    onchange="previewSendImage(event)">
                                
                                <img id="send-image-preview"
                                    src="{{ asset('dist/img/img-plus.png') }}"
                                    class="img-fluid rounded"
                                    style="max-height:120px;">
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="Add any notes ... (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-label-primary btn-round" onclick="return validateDeliveryImage()">
                        <i class="fab fa-telegram-plane me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="copiesModal" tabindex="-1" aria-labelledby="copiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold" id="copiesModalLabel"><i class="fas fa-print me-2"></i> Print Shipment Label</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <label for="copiesInput" class="form-label fw-bold">Enter the number of labels you want to print:</label>
                <input type="number" id="copiesInput" class="form-control" min="1" value="1" autofocus>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmPrintBtn" class="btn btn-label-primary btn-round"><i class="fas fa-print me-1"></i> Print</button>
            </div>
        </div>
    </div>
</div>