document.addEventListener("DOMContentLoaded", function () {
    const activateForm = document.getElementById("activateForm");
    const activateModal = new bootstrap.Modal(
        document.getElementById("activateModal")
    );

    document.addEventListener("click", function (event) {
        if (event.target.closest(".activate-store")) {
            event.preventDefault();
            const link = event.target.closest(".activate-store");
            const storeId = link.getAttribute("data-id");
            activateForm.action = `/master-stores/activate/${storeId}`;
            activateModal.show();
        }
    });
});
