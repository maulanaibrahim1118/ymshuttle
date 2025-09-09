document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-category");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const categoryId = this.getAttribute("data-id");
            deleteForm.action = `/master-categories/destroy/${categoryId}`;
            deleteModal.show();
        });
    });
});
