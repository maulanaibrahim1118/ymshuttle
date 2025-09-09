document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-role");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const roleId = this.getAttribute("data-id");
            deleteForm.action = `/setting-roles/destroy/${roleId}`;
            deleteModal.show();
        });
    });
});
