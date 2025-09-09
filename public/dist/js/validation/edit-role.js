function openEditModal(id, name, guardName) {
    document.getElementById("roleId").value = id;
    document.getElementById("edit_name").value = name;
    document.getElementById("edit_guard_name").value = guardName;
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
        let editName = $("#edit_name").val();
        $.ajax({
            url: "/ajax/check-unique-role-name",
            type: "GET",
            data: { name: editName, id: $("#roleId").val() },
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

    function toggleSubmitButton() {
        if ($("#edit_name").data("changed")) {
            $("#editBtn").prop("disabled", !isNameValid);
        } else {
            $("#editBtn").prop("disabled", !isNameValid);
        }
    }

    $("#edit_name").on("input", function () {
        $(this).data("changed", true);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(validateName, 500); // 500ms delay
    });

    $("#editBtn").prop("disabled", true);
});
