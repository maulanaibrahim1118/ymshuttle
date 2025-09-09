document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-area");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const areaId = this.getAttribute("data-id");
            deleteForm.action = `/master-areas/destroy/${areaId}`;
            deleteModal.show();
        });
    });
});
