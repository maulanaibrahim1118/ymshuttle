@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            @include('contents.delivery.filter')

            <div class="card card-stats card-round" id="shipmentCard">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-boxes me-3"></i>{{ $title }}</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="border-top"></div>
                <div id="collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body pb-0">
                            <div id="searchShipment" class="input-icon mb-2 px-1" style="display: none;">
                                <span class="input-icon-addon">
                                    <i class="fa fa-search ms-1"></i>
                                </span>
                                <input type="text" class="form-control alert-warning" placeholder="Search destination, shipment number or category..." id="searchInput" />
                            </div>
                            
                            <div class="row px-3">
                                <div id="totalData" class="col-8 text-muted small mb-3 px-2"></div>
                                <div id="reloadShipment" class="col-4 text-end small px-2" style="display: none;">
                                    <a href="#" class="text-warning"><i class="fas fa-sync-alt me-1"></i>Reload</a>
                                </div>
                            </div>
                                
                            <div id="shipmentCards" class="row g-3 px-3"></div>

                            <div id="loading" class="wave-loader text-center py-3" style="display: none;">
                                <span></span><span></span><span></span>
                            </div>

                            <div id="scrollSentinel" class="text-center pt-3 text-muted"></div>
                        </div>
                    </div>
                </div>
            </div><!-- End Info Card -->
        </div>
    </div>
</div>
@endsection

@section('customScripts')
<script src="{{ asset('dist/js/app/delivery.js') }}?v={{ config('asset.version') }}"></script>
@endsection
