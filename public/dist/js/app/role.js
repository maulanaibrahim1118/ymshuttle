function showPermissions(encryptedId, roleName) {
    const modalRoleName = document.getElementById("modalRoleName");

    modalRoleName.textContent = roleName;
    $("#permission-content").html(
        '<p class="text-muted">Loading permissions...</p>'
    );

    $("#editPermissionBtn")
        .off("click")
        .on("click", function (e) {
            e.preventDefault();
            $(".modal").modal("hide");
            $("#editPermissionModal").modal("show");
            showEditPermissions(encryptedId, roleName);
        });

    $.ajax({
        url: `/ajax/setting-roles/${encryptedId}/permissions`, // sesuaikan route ini
        method: "GET",
        success: function (response) {
            if ($.isEmptyObject(response)) {
                $("#permission-content").html(
                    '<p class="alert-danger ps-2">No permissions assigned.</p>'
                );
                return;
            }

            let content = "";
            $.each(response, function (group, permissions) {
                content += `<div class="mb-3">`;
                content += `<h6 class="mb-1">${group}:</h6>`;
                $.each(permissions, function (i, perm) {
                    content += `<span class="badge bg-info me-1">${perm.name}</span>`;
                });
                content += `</div>`;
            });

            $("#permission-content").html(content);
        },
        error: function (xhr) {
            $("#permission-content").html(
                `<p class="text-danger">Failed to load permissions: ${
                    xhr.responseJSON?.error || "Unexpected error"
                }</p>`
            );
        },
    });

    $("#permissionModal").modal("show");
}

function showEditPermissions(encryptedId, roleName) {
    $("#editModalRoleName").text(roleName);
    $("#edit-permission-content").html(
        '<p class="text-muted">Loading permissions...</p>'
    );
    $("#editPermissionForm").attr(
        "action",
        `/setting-roles/${encryptedId}/update-permissions`
    );

    $.ajax({
        url: `/ajax/setting-roles/${encryptedId}/permissions-edit`,
        method: "GET",
        success: function (response) {
            if ($.isEmptyObject(response)) {
                $("#edit-permission-content").html(
                    '<p class="text-danger">No permissions found.</p>'
                );
                return;
            }

            let formContent = "";

            $.each(response, function (group, permissions) {
                const groupSlug = group.toLowerCase().replace(/\s+/g, "-");

                formContent += `
                    <div class="form-group mb-3 border rounded pt-0 px-3">
                        <div class="row">
                            <div class="col-6">
                                <h6 class="mb-0 pt-2">${group}</h6>
                            </div>
                            <div class="col-6 px-0">
                                <div class="form-check float-end pt-3">
                                    <input type="checkbox" class="form-check-input select-all-group" data-group="${groupSlug}" id="select-all-${groupSlug}">
                                    <label class="form-check-label" for="select-all-${groupSlug}">Select All</label>
                                </div>
                            </div>
                        </div>
                        <div class="selectgroup selectgroup-pills" id="group-${groupSlug}">
                `;

                $.each(permissions, function (i, perm) {
                    const isChecked = perm.assigned ? "checked" : "";
                    const actionText = perm.action?.toString?.() ?? "";
                    formContent += `
                        <label class="selectgroup-item">
                            <input
                                type="checkbox"
                                class="selectgroup-input permission-checkbox"
                                name="permissions[]"
                                value="${perm.id}"
                                ${isChecked}
                                data-group="${groupSlug}"
                                id="perm-${perm.id}"
                            />
                            <span class="selectgroup-button">${actionText}</span>
                        </label>
                    `;
                });

                formContent += `
                        </div>
                    </div>
                `;
            });

            $("#edit-permission-content").html(formContent);
            $("#editPermissionModal").modal("show");

            // ⬇️ Set status Select All checkbox per grup (saat pertama kali load)
            $(".select-all-group").each(function () {
                const group = $(this).data("group");
                const checkboxes = $(
                    `.permission-checkbox[data-group="${group}"]`
                );
                const checkedCount = checkboxes.filter(":checked").length;

                if (
                    checkedCount === checkboxes.length &&
                    checkboxes.length > 0
                ) {
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });

            // ⬇️ Event: Klik select-all-group akan mengubah semua checkbox satu grup
            $(".select-all-group").on("change", function () {
                const group = $(this).data("group");
                const isChecked = $(this).is(":checked");
                $(`.permission-checkbox[data-group="${group}"]`).prop(
                    "checked",
                    isChecked
                );
            });

            // Optional: Tambahkan listener untuk update "select-all-group" saat user uncheck manual
            $(".permission-checkbox").on("change", function () {
                const group = $(this).data("group");
                const checkboxes = $(
                    `.permission-checkbox[data-group="${group}"]`
                );
                const checkedCount = checkboxes.filter(":checked").length;

                $(`#select-all-${group}`).prop(
                    "checked",
                    checkedCount === checkboxes.length
                );
            });
        },
        error: function (xhr) {
            $("#edit-permission-content").html(
                `<p class="text-danger">Failed to load: ${
                    xhr.responseJSON?.error || "Unexpected error"
                }</p>`
            );
        },
    });
}
