function openEditModal(id, name, pic) {
    document.getElementById("areaId").value = id;
    document.getElementById("edit_name").value = name;

    fillSelectOptions("edit_pic", managers, pic);
}

function fillSelectOptions(selectUsername, data, selectedValue) {
    var selectElement = document.getElementById(selectUsername);
    selectElement.innerHTML = ""; // Kosongkan options

    data.forEach(function (item) {
        var option = new Option(
            item.name.toUpperCase(),
            item.username,
            item.username == selectedValue,
            item.username == selectedValue
        );
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
    let debounceTimer;

    function validateName() {
        let editName = $("#edit_name").val();
        $.ajax({
            url: "/ajax/check-unique-area-name",
            type: "GET",
            data: { name: editName, id: $("#areaId").val() },
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

    function toggleSubmitButton() {
        let allFieldsValid = isNameValid && isPicValid;

        if ($("#edit_name").data("changed") || $("#edit_pic").data("changed")) {
            // If any of these fields changed, ensure validation is successful to enable the button
            $("#editBtn").prop("disabled", !(isNameValid && isPicValid));
        } else {
            $("#editBtn").prop("disabled", !allFieldsValid);
        }
    }

    $("#edit_name").on("input", function () {
        $(this).data("changed", true);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });
    $("#edit_pic").on("change", function () {
        $(this).data("changed", true);
        validatePic();
    });

    $("#editBtn").prop("disabled", true);
});
