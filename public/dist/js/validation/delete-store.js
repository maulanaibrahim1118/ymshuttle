document.addEventListener("DOMContentLoaded", function () {
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    // Event delegation untuk elemen dinamis dari DataTables
    document.addEventListener("click", function (event) {
        if (event.target.closest(".delete-store")) {
            event.preventDefault();
            const link = event.target.closest(".delete-store");
            const storeId = link.getAttribute("data-id");
            deleteForm.action = `/master-stores/destroy/${storeId}`;
            deleteModal.show();
        }
    });
});
