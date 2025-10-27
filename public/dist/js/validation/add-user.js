$(document).ready(function () {
    let isNameValid = $("#name").val() ? true : false;
    let isUsernameUnique = $("#username").val() ? true : false;
    let isLocationValid = $("#location").val() ? true : false;
    let isRoleValid = $("#role").val() ? true : false;
    const userId = $("#id").val();
    let debounceTimer;

    function filterNonNumericInput(input) {
        return input.replace(/[^0-9]/g, "");
    }

    function validateName() {
        isNameValid = !!$("#name").val();
        toggleSubmitButton();
    }

    function validateUsername() {
        let username = $("#username").val();
        username = filterNonNumericInput(username);
        $("#username").val(username);

        if (username !== "" && /^[0-9a-zA-Z]{1,10}$/.test(username)) {
            $.ajax({
                url: "/ajax/check-unique-username",
                type: "GET",
                data: { username: username, id: userId },
                beforeSend: function () {
                    $("#submitBtn").prop("disabled", true);
                },
                success: function (response) {
                    isUsernameUnique = response.unique;
                    $("#username-taken").toggle(!isUsernameUnique);
                    $(".unique-username").toggleClass(
                        "has-error",
                        !isUsernameUnique
                    );
                },
                error: function () {
                    $("#username-taken")
                        .text("Error checking username uniqueness.")
                        .show();
                    isCodeValid = false;
                    $(".unique-username").toggleClass(
                        "has-error",
                        !isUsernameUnique
                    );
                },
                complete: function () {
                    toggleSubmitButton();
                },
            });
        } else {
            $("#username-taken").hide();
            $(".unique-username").removeClass("has-error");
            isUsernameUnique = false;
            toggleSubmitButton();
        }
    }

    function validateLocation() {
        isLocationValid = !!$("#location").val();
        toggleSubmitButton();
    }

    function validateRole() {
        isRoleValid = !!$("#role").val();
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        let isFormValid =
            isUsernameUnique && isNameValid && isLocationValid && isRoleValid;
        $("#submitBtn").prop("disabled", !isFormValid);
    }

    $("#name").on("input", validateName);
    $("#username").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateUsername, 500); // 500ms delay
    });
    $("#location").on("change", validateLocation);
    $("#role").on("change", validateRole);

    toggleSubmitButton();
});
