@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            @include('contents.setting.user.create')

            <div class="card card-stats card-round" id="importCard">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-user-circle me-3"></i>{{ $title }}</h5>
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
                                    @can('user-import')
                                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-block" id="importForm">
                                        @csrf
                                        <div class="card-tools float-start mb-3">
                                            <label for="import_file" class="btn btn-label-success btn-round btn-sm me-2 mb-0 d-flex align-items-center" style="cursor: pointer;" id="importBtn">
                                                <span class="btn-label me-1">
                                                    <i class="fa fa-file-import"></i>
                                                </span>
                                                <span id="importText">Import</span>
                                            </label>
                                            <input type="file" name="import_file" id="import_file" accept=".xlsx,.csv" hidden>
                                        </div>
                                    </form>
                                    @endcan
                                    <div class="table-responsive">
                                        <table id="user-datatables" class="display table table-hover text-nowrap">
                                            <thead class="bg-light" style="height: 45px;font-size:14px;">
                                                <tr>
                                                @canany(['user-edit', 'user-reset-password', 'user-deactivate', 'user-activate'])
                                                <th scope="col">Action</th>
                                                @endcanany
                                                <th scope="col">Username</th>
                                                <th scope="col">Full Name</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Role</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Last Login</th>
                                                <th scope="col">Created By</th>
                                                <th scope="col">Updated By</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
                                                <tr class="text-uppercase">
                                                @canany(['user-edit', 'user-reset-password', 'user-deactivate', 'user-activate'])
                                                <td>
                                                    @if($user->id != 1)
                                                    @can('user-edit')
                                                    <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEditModal('{{ encrypt($user->id) }}', '{{ $user->username }}', '{{ $user->name }}', '{{ $user->location_code }}', '{{ $user->getRoleNames()->first() }}')" title="Edit">
                                                        <i class="fas fa-edit text-primary"></i>
                                                    </a>
                                                    @endcan
                                                    @endif

                                                    @can('user-reset-password')
                                                    <a href="#" class="reset-password me-3" data-id="{{ encrypt($user->id) }}" title="Reset Password">
                                                        <i class="fas fa-key text-warning"></i>
                                                    </a>
                                                    @endcan

                                                    @if($user->id != 1)
                                                    @if($user->is_active == 1 && auth()->user()->can('user-deactivate'))
                                                    <a href="#" class="deactivate-user" data-id="{{ encrypt($user->id) }}" title="Deactivate">
                                                        <i class="fas fa-power-off text-danger"></i>
                                                    </a>
                                                    @endif

                                                    @if($user->is_active == 0 && auth()->user()->can('user-activate'))
                                                    <a href="#" class="activate-user" data-id="{{ encrypt($user->id) }}" title="Activate">
                                                        <i class="fas fa-power-off text-success"></i>
                                                    </a>
                                                    @endif
                                                    @endif
                                                </td>
                                                @endcanany
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->location->name }}</td>
                                                <td>{{ $user->getRoleNames()->first() }}</td>
                                                @if($user->is_active == '1')
                                                <td><span class="badge bg-success">Active</span></td>
                                                @else
                                                <td><span class="badge bg-danger">Inactive</span></td>
                                                @endif
                                                <td>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d-M-y H:i:s') : '-' }}</td>
                                                <td>{{ $user->created_by }}</td>
                                                <td>{{ $user->updated_by }}</td>
                                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-M-y H:i:s') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($user->updated_at)->format('d-M-y H:i:s') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
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

{{-- Modals --}}
@include('contents.setting.user.edit')
@include('contents.setting.user.resetPassword')
@include('contents.setting.user.deactivate')
@include('contents.setting.user.activate')
@endsection

@section('customScripts')
<script>
    var locations = @json($locations);
    var roles = @json($roles);
    
    $(document).ready(function () {
        $("#user-datatables").DataTable({
            order: [
                [@canany(['user-edit', 'user-reset-password', 'user-deactivate', 'user-activate']) 5 @else 4 @endcanany, 'asc'], // Kolom "Status"
                [@canany(['user-edit', 'user-reset-password', 'user-deactivate', 'user-activate']) 2 @else 0 @endcanany, 'asc'] // Kolom "Full Name"
            ]
        });
    });

    document.getElementById('import_file').addEventListener('change', function () {
        // Submit form
        document.getElementById('importForm').submit();
    });
</script>
<script src="{{ asset('dist/js/validation/add-user.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/edit-user.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/reset-pass.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/deactivate-user.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/activate-user.js') }}?v={{ config('asset.version') }}"></script>
@endsection