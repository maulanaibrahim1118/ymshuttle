$(function () {
    let isNameUnique = $("#name").val() ? true : false;
    let isPicValid = $("#pic").val() ? true : false;
    let isRegionalValid = $("#regional_id").val() ? true : false;
    const wilayahId = $("#id").val();
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
                url: "/ajax/check-unique-wilayah-name",
                type: "GET",
                data: { name: name, id: wilayahId },
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

    function validateRegional() {
        isRegionalValid = $("#regional_id").val() !== "";
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        let isFormValid = isNameUnique && isPicValid && isRegionalValid;
        $("#submitBtn").prop("disabled", !isFormValid);
    }

    $("#name").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });
    $("#pic").on("change", validatePic);
    $("#regional_id").on("change", validateRegional);

    toggleSubmitButton();
});
