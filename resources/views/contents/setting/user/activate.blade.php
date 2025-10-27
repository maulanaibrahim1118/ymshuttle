@can('user-activate')
<div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-bold" id="activateModalLabel"><i class="fas fa-question-circle me-2"></i>Confirm Activate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure, want to activate this user?</p>
                <div class="border-top"></div>
                <div class="text-end mt-3">
                    <form id="activateForm" method="POST" action="">
                        @csrf
                        <button type="button" class="btn btn-light btn-round me-2" data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" class="btn btn-label-success btn-round"><i class="fas fa-power-off me-2"></i>Yes, activate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan