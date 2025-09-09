function openEditModal(id, name) {
    $("#categoryId").val(id);
    $("#edit_name").val(name);
    fetchCategoryDetails(id); // tambahan untuk fetch criterias
    setTimeout(() => {
        validateEditName(); // trigger validasi nama
        validateEditCriteria(); // trigger validasi kriteria
    }, 300); // beri delay agar DOM ready
}

function fetchCategoryDetails(id) {
    $.ajax({
        url: `/ajax/master-categories/${id}/get-criterias`,
        type: "GET",
        success: function (response) {
            let criterias = response.criterias; // selected IDs [1, 3, ...]
            let allCriterias = response.all_criterias; // full list [{id, name}, ...]

            let html = "";

            allCriterias.forEach(function (criteria) {
                let selected = criterias.includes(criteria.id)
                    ? "selected"
                    : "";
                html += `<option value="${
                    criteria.id
                }" ${selected}>${criteria.name.toUpperCase()}</option>`;
            });

            const $select = $("#edit_criterias");
            $select.html(html).trigger("change"); // update DOM & Select2
            validateEditCriteria(); // langsung validasi juga
        },
        error: function () {
            $("#edit_criterias").html(""); // kosongkan jika error
        },
    });
}

// Menghilangkan pesan kesalahan saat modal edit di close
$("#editModal").on("hidden.bs.modal", function () {
    $("#edit_name-taken").hide();
    $(".unique-edit-name").removeClass("has-error");
});

$(function () {
    let isNameValid = true;
    let isCriteriaValid = true;
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

    function validateCriteria() {
        const selected = $("#edit_criterias").val();
        isCriteriaValid = Array.isArray(selected) && selected.length > 0;
        $("#editCriteriaError").toggleClass("d-none", isCriteriaValid);
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        const allFieldsValid = isNameValid && isCriteriaValid;

        if (
            $("#edit_name").data("changed") ||
            $("input[name='edit_criterias[]']").data("changed")
        ) {
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

    $("#edit_criterias").on("change", validateCriteria);

    $("#editBtn").prop("disabled", true);
});
