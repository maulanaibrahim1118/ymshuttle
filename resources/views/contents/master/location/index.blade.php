@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            @include('contents.master.location.filter')

            <div class="card card-stats card-round" id="storeCard">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-3"></i>{{ $title }}</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="border-top"></div>
                <div id="collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body">
                            <div class="row" id="tableWrapper">
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive mt-3">
                                        <table id="location-datatables" class="display table table-hover text-nowrap w-100">
                                            <thead class="bg-light" style="height: 45px;font-size:14px;">
                                                <tr>
                                                <th scope="col">Location Name</th>
                                                <th scope="col">Address</th>
                                                <th scope="col">City</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">DC Support</th>
                                                <th scope="col">No. Telp</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Created By</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Updated By</th>
                                                <th scope="col">Updated At</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Info Card -->
        </div>
    </div>
</div>
@endsection

@section('customScripts')
<script src="{{ asset('dist/js/app/locations.js') }}?v={{ config('asset.version') }}"></script>
@endsection