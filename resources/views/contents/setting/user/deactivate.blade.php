@can('user-deactivate')
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-bold" id="deactivateModalLabel"><i class="fas fa-question-circle me-2"></i>Confirm Deactivate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure, want to deactivate this user?</p>
                <div class="border-top"></div>
                <div class="text-end mt-3">
                    <form id="deactivateForm" method="POST" action="">
                        @csrf
                        <button type="button" class="btn btn-light btn-round me-2" data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" class="btn btn-label-danger btn-round"><i class="fas fa-power-off me-2"></i>Yes, deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan