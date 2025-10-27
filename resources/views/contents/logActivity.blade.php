@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">

            <div class="card card-stats card-round">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h5 class="card-title mb-0"><i class="fas fa-filter me-3"></i>Filter</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="border-top"></div>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body">
                            <form id="filter-form" class="row g-3 p-3">
                                @csrf
                                <div class="col-md-3 mt-3">
                                    <select name="username" class="form-select select2" id="username">
                                        <option selected value="">ALL ACTORS</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->username }}">{{ strtoupper($user->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <select name="subject" class="form-select select2" id="subject">
                                        <option selected value="">ALL SUBJECTS</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->name }}">{{ strtoupper(str_replace('-', ' ', $subject->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <button type="submit" id="filterBtn" class="btn btn-warning text-light"><i class="fas fa-filter me-1"></i> Filter</button>
                                </div>
                            </form><!-- End Input Form -->
                        </div>
                    </div>
                </div>
            </div><!-- End Info Card -->

            <div class="card card-stats card-round">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-history me-3"></i>{{ $title }}</h5>
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
                                    <div class="table-responsive">
                                        <table id="log-datatables" class="display table table-hover text-nowrap">
                                            <thead class="bg-light" style="height: 45px;font-size:14px;">
                                                <tr>
                                                <th scope="col">Date Time</th>
                                                <th scope="col">Actor</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Message</th>
                                                <th scope="col">IP Address</th>
                                                <th scope="col">Agent</th>
                                                <th scope="col">URL</th>
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

<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title" id="errorModalLabel"><i class="fas fa-comment-dots me-2"></i>Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <div id="errorDetail" class="mb-3"></div>
                <div class="border-top"></div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customScripts')
<script>
    const listUrl = "{{ route('logActivities.list') }}";
</script>
<script src="{{ asset('dist/js/app/log-activities.js') }}?v={{ config('asset.version') }}"></script>
@endsection