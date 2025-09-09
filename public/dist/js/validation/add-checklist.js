$(function () {
    let isStoreValid = $("#store_code").val() ? true : false;
    let isCategoryValid = $("#category_id").val() ? true : false;
    let isStaffOnDutyValid = $("#staff_on_duty").val() ? true : false;

    function validateStore() {
        isStoreValid = $("#store_code").val() !== "";
        toggleSubmitButton();
    }
    function validateCategory() {
        isCategoryValid = $("#category_id").val() !== "";
        toggleSubmitButton();
    }
    function validateStaffOnDuty() {
        isStaffOnDutyValid = $("#staff_on_duty").val() !== "";
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        const isFormValid =
            isStoreValid && isCategoryValid && isStaffOnDutyValid;
        $("#submitBtn").prop("disabled", !isFormValid);
    }

    $("#store_code").on("change", validateStore);
    $("#category_id").on("change", validateCategory);
    $("#staff_on_duty").on("input", validateStaffOnDuty);

    toggleSubmitButton();
});
