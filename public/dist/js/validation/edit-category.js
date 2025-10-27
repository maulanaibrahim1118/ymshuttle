function openEditModal(id, name) {
    $("#categoryId").val(id);
    $("#edit_name").val(name);
}

// Menghilangkan pesan kesalahan saat modal edit di close
$("#editModal").on("hidden.bs.modal", function () {
    $("#edit_name-taken").hide();
    $(".unique-edit-name").removeClass("has-error");
});

$(function () {
    let isNameValid = true;
    let debounceTimer;

    function validateName() {
        let editName = $("#edit_name").val().trim();

        if (editName === "") {
            isNameValid = false;
            $("#edit_name-taken").text("Category Name is required.").show();
            $(".unique-edit-name").addClass("has-error");
            toggleSubmitButton();
            return;
        }

        $.ajax({
            url: "/ajax/check-unique-category-name",
            type: "GET",
            data: { name: editName, id: $("#categoryId").val() },
            success: function (response) {
                isNameValid = response.unique;
                $("#edit_name-taken")
                    .text("Category Name exists!")
                    .toggle(!isNameValid);
                $(".unique-edit-name").toggleClass("has-error", !isNameValid);
                toggleSubmitButton();
            },
            error: function () {
                $("#edit_name-taken")
                    .text("Error checking name uniqueness.")
                    .show();
                $(".unique-edit-name").addClass("has-error");
                isNameValid = false;
                toggleSubmitButton();
            },
        });
    }

    function toggleSubmitButton() {
        const allFieldsValid = isNameValid;

        if ($("#edit_name").data("changed")) {
            $("#editBtn").prop("disabled", !allFieldsValid);
        } else {
            $("#editBtn").prop("disabled", !allFieldsValid);
        }
    }

    $("#edit_name").on("input", function () {
        $(this).data("changed", true);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });

    $("#editBtn").prop("disabled", true);
});
