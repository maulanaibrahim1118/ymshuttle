document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-cluster");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const clusterId = this.getAttribute("data-id");
            deleteForm.action = `/master-clusters/destroy/${clusterId}`;
            deleteModal.show();
        });
    });
});
