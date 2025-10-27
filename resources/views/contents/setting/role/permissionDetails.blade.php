@can('role-permission')
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title px-2 fw-bold"><i class="fas fa-shield-alt me-2"></i>Permissions: <span id="modalRoleName" class="text-warning"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4" id="permission-content">
                <p class="text-center text-muted">Loading permissions...</p>
            </div>
            <div class="modal-footer float-end">
                <button type="button" class="btn btn-light btn-round" data-bs-dismiss="modal">Close</button>
                @can('role-edit-permission')
                <a href="#" id="editPermissionBtn" class="btn btn-label-primary btn-round">
                    <i class="fas fa-edit me-1"></i> Edit Permissions
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endcan