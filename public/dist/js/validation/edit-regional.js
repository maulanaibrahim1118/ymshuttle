function openEditModal(id, name, pic, admin, area) {
    document.getElementById("regionalId").value = id;
    document.getElementById("edit_name").value = name;

    fillSelectOptions("edit_pic", chiefs, pic, "username", true);
    fillSelectOptions("edit_admin", admins, admin, "username", true);
    fillSelectOptions("edit_area", areas, area, "id", true);
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
    $("#edit_name-taken").hide();
    $(".unique-edit-name").removeClass("has-error");
});

$(function () {
    let isNameValid = true;
    let isPicValid = true;
    let isAdminValid = true;
    let isAreaValid = true;
    let debounceTimer;

    function validateName() {
        let editName = $("#edit_name").val();
        $.ajax({
            url: "/ajax/check-unique-regional-name",
            type: "GET",
            data: { name: editName, id: $("#regionalId").val() },
            success: function (response) {
                isNameValid = response.unique;
                $("#edit_name-taken").toggle(!isNameValid);
                $(".unique-edit-name").toggleClass("has-error", !isNameValid);
                toggleSubmitButton();
            },
            error: function () {
                $("#edit_name-taken")
                    .text("Error checking name uniqueness.")
                    .show();
                $(".unique-edit-name").toggleClass("has-error", !isNameValid);
                isNameValid = false;
                toggleSubmitButton();
            },
        });
    }

    function validatePic() {
        isPicValid = $("#edit_pic").val() !== "";
        toggleSubmitButton();
    }

    function validateAdmin() {
        isAdminValid = $("#edit_admin").val() !== "";
        toggleSubmitButton();
    }

    function validateArea() {
        isAreaValid = $("#edit_area").val() !== "";
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        let allFieldsValid =
            isNameValid && isPicValid && isAdminValid && isAreaValid;

        if (
            $("#edit_name").data("changed") ||
            $("#edit_pic").data("changed") ||
            $("#edit_admin").data("changed") ||
            $("#edit_area").data("changed")
        ) {
            // If any of these fields changed, ensure validation is successful to enable the button
            $("#editBtn").prop(
                "disabled",
                !(isNameValid && isPicValid && isAdminValid && isAreaValid)
            );
        } else {
            $("#editBtn").prop("disabled", !allFieldsValid);
        }
    }

    $("#edit_name").on("input", function () {
        $(this).data("changed", true);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });
    $("#edit_admin").on("change", function () {
        $(this).data("changed", true);
        validateAdmin();
    });
    $("#edit_pic").on("change", function () {
        $(this).data("changed", true);
        validatePic();
    });
    $("#edit_area").on("change", function () {
        $(this).data("changed", true);
        validateArea();
    });

    $("#editBtn").prop("disabled", true);
});
