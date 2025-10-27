@can('user-reset-password')
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-bold" id="resetModalLabel"><i class="fas fa-question-circle me-2"></i>Confirm Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure, want to reset password this user?</p>
                <div class="border-top"></div>
                <div class="text-end mt-3">
                    <form id="resetForm" method="POST" action="">
                        @csrf
                        <button type="button" class="btn btn-light btn-round me-2" data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" class="btn btn-label-warning btn-round"><i class="fas fa-key me-2"></i>Yes, reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan