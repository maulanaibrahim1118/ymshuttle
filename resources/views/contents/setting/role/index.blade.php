@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            @include('contents.setting.role.create')

            <div class="card card-stats card-round">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-user-shield me-3"></i>{{ $title }}</h5>
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
                                        <table id="role-datatables" class="display table table-hover text-nowrap">
                                            <thead class="bg-light" style="height: 45px;font-size:14px;">
                                                <tr>
                                                @canany(['role-edit', 'role-delete'])
                                                <th scope="col">Action</th>
                                                @endcanany
                                                <th scope="col">Role Name</th>
                                                <th scope="col">Guard Name</th>
                                                @can('role-permission')
                                                <th scope="col">Permission</th>
                                                @endcan
                                                <th scope="col">Created At</th>
                                                <th scope="col">Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($roles as $role)
                                                <tr class="text-uppercase">
                                                @canany(['role-edit', 'role-delete'])
                                                <td>
                                                    @can('role-edit')
                                                    <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEditModal('{{ encrypt($role->id) }}', '{{ $role->name }}', '{{ $role->guard_name }}')" title="Edit">
                                                        <i class="fas fa-edit text-primary"></i>
                                                    </a>
                                                    @endcan
                                                    @can('role-delete')
                                                    <a href="#" class="delete-role" data-id="{{ encrypt($role->id) }}" title="Delete">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                                @endcanany
                                                <td>{{ $role->name }}</td>
                                                <td>{{ $role->guard_name }}</td>
                                                @can('role-permission')
                                                <td>
                                                    <a href="#" class="text-capitalize text-info" onclick="showPermissions('{{ Crypt::encrypt($role->id) }}', '{{ ucwords($role->name) }}')">
                                                        <i class="fas fa-shield-alt me-1"></i>View Details
                                                    </a>
                                                </td>
                                                @endcan
                                                <td>{{ \Carbon\Carbon::parse($role->created_at)->format('d-M-y H:i:s') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($role->updated_at)->format('d-M-y H:i:s') }}</td>
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
@include('contents.setting.role.permissionDetails')
@include('contents.setting.role.permissionEdit')
@include('contents.setting.role.edit')
@include('contents.setting.role.delete')
@endsection

@section('customScripts')
<script>
    $(document).ready(function () {
        $("#role-datatables").DataTable({});
    });
</script>

<script src="{{ asset('dist/js/app/role.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/add-role.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/edit-role.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/delete-role.js') }}?v={{ config('asset.version') }}"></script>
@endsection