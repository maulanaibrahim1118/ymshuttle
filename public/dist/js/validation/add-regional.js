$(function () {
    let isNameUnique = $("#name").val() ? true : false;
    let isPicValid = $("#pic").val() ? true : false;
    let isAdminValid = $("#admin").val() ? true : false;
    let isAreaValid = $("#area_id").val() ? true : false;
    const regionalId = $("#id").val();
    let debounceTimer;

    function validateName() {
        let name = $("#name").val();
        if (!name) {
            $("#name-taken").hide();
            isNameUnique = false;
            $(".unique-name").removeClass("has-error");
            toggleSubmitButton();
        } else {
            $.ajax({
                url: "/ajax/check-unique-regional-name",
                type: "GET",
                data: { name: name, id: regionalId },
                beforeSend: function () {
                    $("#submitBtn").prop("disabled", true);
                },
                success: function (response) {
                    isNameUnique = response.unique;
                    $("#name-taken").toggle(!isNameUnique);
                    $(".unique-name").toggleClass("has-error", !isNameUnique);
                    $(".required-name").hide();
                },
                error: function () {
                    $("#name-taken")
                        .text("Error checking name uniqueness.")
                        .show();
                    isNameUnique = false;
                    $(".unique-name").toggleClass("has-error", !isNameUnique);
                    $(".required-name").hide();
                },
                complete: function () {
                    toggleSubmitButton();
                },
            });
        }
    }

    function validatePic() {
        isPicValid = $("#pic").val() !== "";
        toggleSubmitButton();
    }

    function validateAdmin() {
        isAdminValid = $("#admin").val() !== "";
        toggleSubmitButton();
    }

    function validateArea() {
        isAreaValid = $("#area_id").val() !== "";
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        let isFormValid =
            isNameUnique && isPicValid && isAdminValid && isAreaValid;
        $("#submitBtn").prop("disabled", !isFormValid);
    }

    $("#name").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });
    $("#pic").on("change", validatePic);
    $("#admin").on("change", validateAdmin);
    $("#area_id").on("change", validateArea);

    toggleSubmitButton();
});
