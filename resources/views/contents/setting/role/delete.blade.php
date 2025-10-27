@can('role-delete')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-bold" id="deleteModalLabel"><i class="fas fa-question-circle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure, want to delete this role?</p>
                <div class="border-top"></div>
                <div class="text-end mt-3">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light btn-round me-2" data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" class="btn btn-label-danger btn-round"><i class="fas fa-trash-alt me-2"></i>Yes, delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan