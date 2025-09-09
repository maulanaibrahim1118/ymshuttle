document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-regional");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const regionalId = this.getAttribute("data-id");
            deleteForm.action = `/master-regionals/destroy/${regionalId}`;
            deleteModal.show();
        });
    });
});
