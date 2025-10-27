@can('role-edit')
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-bold" id="editModalLabel"><i class="fas fa-edit me-2"></i>Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" action="{{ route('roles.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="roleId" name="id">
                    <div class="mb-3 row unique-edit-name">
                        <label class="col-4 col-form-label">Role Name</label>
                        <div class="col-8">
                            <input type="text" class="form-control alert-warning text-uppercase" id="edit_name" name="edit_name" required>
                            <label id="edit_name-taken" style="display: none;">Role Name exists!</label>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-4 col-form-label">Guard Name</label>
                        <div class="col-8">
                            <input type="text" name="guard_name" class="form-control alert-warning text-uppercase" id="edit_guard_name" readonly>
                        </div>
                    </div>
                    <div class="border-top"></div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-light btn-round me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="editBtn" class="btn btn-label-primary btn-round" disabled><i class="fas fa-save me-2"></i>Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan