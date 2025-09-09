document.addEventListener("DOMContentLoaded", function () {
    const deactivateLinks = document.querySelectorAll(".deactivate-user");
    const deactivateForm = document.getElementById("deactivateForm");
    const deactivateModal = new bootstrap.Modal(
        document.getElementById("deactivateModal")
    );

    deactivateLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const userId = this.getAttribute("data-id");
            deactivateForm.action = `/setting-users/deactivate/${userId}`;
            deactivateModal.show();
        });
    });
});
