document.addEventListener("DOMContentLoaded", function () {
    const resetLinks = document.querySelectorAll(".reset-password");
    const resetForm = document.getElementById("resetForm");
    const resetModal = new bootstrap.Modal(
        document.getElementById("resetModal")
    );

    resetLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const userId = this.getAttribute("data-id");
            resetForm.action = `/setting-users/reset-pass/${userId}`;
            resetModal.show();
        });
    });
});
