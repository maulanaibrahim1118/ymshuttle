$(function () {
    let isNameUnique = $("#name").val() ? true : false;
    const roleId = $("#id").val();
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
                url: "/ajax/check-unique-role-name",
                type: "GET",
                data: { name: name, id: roleId },
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

    function toggleSubmitButton() {
        $("#submitBtn").prop("disabled", !isNameUnique);
    }

    $("#name").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });

    toggleSubmitButton();
});
