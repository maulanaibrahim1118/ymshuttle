@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            @include('contents.master.category.create')

            <div class="card card-stats card-round">
                <div class="card-body pb-0">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0"><i class="fas fa-shapes me-3"></i>{{ $title }}</h5>
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
                                        <table id="category-datatables" class="display table table-hover text-nowrap">
                                            <thead class="bg-light" style="height: 45px;font-size:14px;">
                                                <tr>
                                                @canany(['category-edit', 'category-delete'])
                                                <th scope="col">Action</th>
                                                @endcanany
                                                <th scope="col">Category Name</th>
                                                <th scope="col">Created By</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Updated By</th>
                                                <th scope="col">Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categories as $category)
                                                <tr class="text-uppercase">
                                                @canany(['category-edit', 'category-delete'])
                                                <td>
                                                    @can('category-edit')
                                                    <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEditModal('{{ encrypt($category->id) }}', '{{ $category->name }}')" title="Edit">
                                                        <i class="fas fa-edit text-primary"></i>
                                                    </a>
                                                    @endcan
                                                    @can('category-delete')
                                                    <a href="#" class="delete-category" data-id="{{ encrypt($category->id) }}" title="Delete">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                                @endcanany
                                                <td>{{ $category->name }}</td>
                                                <td>{{ optional($category->creator)->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($category->created_at)->format('d-M-y H:i:s') }}</td>
                                                <td>{{ optional($category->updater)->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($category->updated_at)->format('d-M-y H:i:s') }}</td>
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
@include('contents.master.category.edit')
@include('contents.master.category.delete')
@endsection

@section('customScripts')
<script>
    $(document).ready(function () {
        $("#category-datatables").DataTable({
            order: [
                [@canany(['category-edit', 'category-delete']) 1 @else 0 @endcanany, 'asc'],
            ]
        });
    });
</script>

<script src="{{ asset('dist/js/validation/add-category.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/edit-category.js') }}?v={{ config('asset.version') }}"></script>
<script src="{{ asset('dist/js/validation/delete-category.js') }}?v={{ config('asset.version') }}"></script>
@endsection