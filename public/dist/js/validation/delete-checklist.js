document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-checklist");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const checklistId = this.getAttribute("data-id");
            deleteForm.action = `/checklists/destroy/${checklistId}`;
            deleteModal.show();
        });
    });
});
