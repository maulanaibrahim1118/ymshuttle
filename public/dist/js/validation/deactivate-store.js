document.addEventListener("DOMContentLoaded", function () {
    const deactivateForm = document.getElementById("deactivateForm");
    const deactivateModal = new bootstrap.Modal(
        document.getElementById("deactivateModal")
    );

    document.addEventListener("click", function (event) {
        if (event.target.closest(".deactivate-store")) {
            event.preventDefault();
            const link = event.target.closest(".deactivate-store");
            const storeId = link.getAttribute("data-id");
            deactivateForm.action = `/master-stores/deactivate/${storeId}`;
            deactivateModal.show();
        }
    });
});
