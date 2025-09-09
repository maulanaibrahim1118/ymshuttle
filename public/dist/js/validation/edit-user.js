function openEditModal(id, username, name, positionId, roleName) {
    document.getElementById("userId").value = id;
    document.getElementById("edit_username").value = username;
    document.getElementById("edit_name").value = name;

    fillSelectOptions("edit_position", positions, positionId, "id", true); // pakai ID, label uppercase
    fillSelectOptions("edit_role", roles, roleName, "name", true); // pakai name, label uppercase
}

function fillSelectOptions(
    selectId,
    data,
    selectedValue,
    valueField = "id",
    toUpper = false
) {
    const selectElement = document.getElementById(selectId);
    selectElement.innerHTML = "";

    data.forEach((item) => {
        const value = item[valueField];
        const label = toUpper ? item.name.toUpperCase() : item.name;
        const isSelected = value == selectedValue;

        const option = new Option(label, value, isSelected, isSelected);
        selectElement.append(option);
    });
}

// Menghilangkan pesan kesalahan saat modal edit di close
$("#editModal").on("hidden.bs.modal", function () {
    $("#edit_username-taken").hide();
    $(".unique-edit-username").removeClass("has-error");
});

$(function () {
    let isNameValid = true;
    let isUsernameValid = true;
    let isPositionValid = true;
    let isRoleValid = true;
    let debounceTimer;

    function filterNonNumericInput(input) {
        return input.replace(/[^0-9]/g, "");
    }

    function validateName() {
        isNameValid = $("#edit_name").val() !== "";
        toggleSubmitButton();
    }

    function validateUsername() {
        let editUsername = $("#edit_username").val();
        editUsername = filterNonNumericInput(editUsername);
        $("#edit_username").val(editUsername); // Update the input with filtered value

        if (editUsername !== "" && /^[0-9a-zA-Z]{1,10}$/.test(editUsername)) {
            $.ajax({
                url: "/ajax/check-unique-username",
                type: "GET",
                data: { username: editUsername, id: $("#userId").val() },
                success: function (response) {
                    isUsernameValid = response.unique;
                    $("#edit_username-taken").toggle(!isUsernameValid);
                    $(".unique-edit-username").toggleClass(
                        "has-error",
                        !isUsernameValid
                    );
                    toggleSubmitButton();
                },
                error: function () {
                    $("#edit_username-taken")
                        .text("Error checking username uniqueness.")
                        .show();
                    isUsernameValid = false;
                    $(".unique-edit-username").toggleClass(
                        "has-error",
                        !isUsernameValid
                    );
                    toggleSubmitButton();
                },
            });
        } else {
            $("#edit_username-taken").hide();
            $(".unique-edit-username").removeClass("has-error");
            isUsernameValid = false;
            toggleSubmitButton();
        }
    }

    function validatePosition() {
        isPositionValid = $("#edit_position").val() !== "";
        toggleSubmitButton();
    }

    function validateRole() {
        isRoleValid = $("#edit_role").val() !== "";
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        let allFieldsValid =
            isNameValid && isUsernameValid && isPositionValid && isRoleValid;

        if (
            $("#edit_name").data("changed") ||
            $("#edit_username").data("changed") ||
            $("#edit_position").data("changed") ||
            $("#edit_role").data("changed")
        ) {
            // If any of these fields changed, ensure validation is successful to enable the button
            $("#editBtn").prop(
                "disabled",
                !(
                    isNameValid &&
                    isUsernameValid &&
                    isPositionValid &&
                    isRoleValid
                )
            );
        } else {
            $("#editBtn").prop("disabled", !allFieldsValid);
        }
    }

    // Attach event handlers
    $("#edit_name").on("input", function () {
        $(this).data("changed", true);
        validateName();
    });
    $("#edit_username").on("input", function () {
        $(this).data("changed", true);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateUsername, 500); // 500ms delay
    });
    $("#edit_position").on("change", function () {
        $(this).data("changed", true);
        validatePosition();
    });
    $("#edit_role").on("change", function () {
        $(this).data("changed", true);
        validateRole();
    });

    // Initially disable the button
    $("#editBtn").prop("disabled", true);
});
