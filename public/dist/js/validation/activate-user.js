document.addEventListener("DOMContentLoaded", function () {
    const activateLinks = document.querySelectorAll(".activate-user");
    const activateForm = document.getElementById("activateForm");
    const activateModal = new bootstrap.Modal(
        document.getElementById("activateModal")
    );

    activateLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const userId = this.getAttribute("data-id");
            activateForm.action = `/setting-users/activate/${userId}`;
            activateModal.show();
        });
    });
});
