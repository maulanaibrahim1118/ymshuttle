$(function () {
    let isNameUnique = $("#name").val() ? true : false;
    let isCriteriaValid =
        Array.isArray($("#criterias").val()) &&
        $("#criterias").val().length > 0;
    const categoryId = $("#id").val();
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
                url: "/ajax/check-unique-category-name",
                type: "GET",
                data: { name: name, id: categoryId },
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

    function validateCriteria() {
        isCriteriaValid =
            Array.isArray($("#criterias").val()) &&
            $("#criterias").val().length > 0;
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        const isFormValid = isNameUnique && isCriteriaValid;
        $("#submitBtn").prop("disabled", !isFormValid);
    }

    $("#name").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });

    $("#criterias").on("change", validateCriteria);

    toggleSubmitButton();
});
