document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-criteria");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const criteriaId = this.getAttribute("data-id");
            deleteForm.action = `/master-criterias/destroy/${criteriaId}`;
            deleteModal.show();
        });
    });
});
