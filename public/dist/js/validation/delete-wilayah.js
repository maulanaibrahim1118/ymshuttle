document.addEventListener("DOMContentLoaded", function () {
    const deleteLinks = document.querySelectorAll(".delete-wilayah");
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    deleteLinks.forEach((link) => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const wilayahId = this.getAttribute("data-id");
            deleteForm.action = `/master-wilayahs/destroy/${wilayahId}`;
            deleteModal.show();
        });
    });
});
