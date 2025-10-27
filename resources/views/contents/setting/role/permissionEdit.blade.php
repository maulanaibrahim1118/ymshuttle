@can('role-edit-permission')
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editPermissionForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title px-2 fw-bold"><i class="fas fa-edit me-2"></i>Edit Permissions: <span id="editModalRoleName" class="text-warning"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4" id="edit-permission-content">
                    <p class="text-center text-muted">Loading permissions...</p>
                </div>
                <div class="modal-footer float-end">
                    <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-label-primary btn-round">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan