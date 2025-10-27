document.addEventListener("DOMContentLoaded", function () {
    let rowIdx = 1;

    // Add Row
    document.getElementById("addRow").addEventListener("click", function () {
        let container = document.getElementById("items_container");

        let newRow = document.createElement("div");
        newRow.classList.add("item-row", "border", "rounded", "p-3", "mb-2");
        newRow.innerHTML = `
                <div class="row g-3 px-2">
                    <div class="col-12 col-md-4 px-2">
                        <input type="text" name="items[${rowIdx}][name]" class="form-control text-uppercase alert-warning" placeholder="Item Name*" required>
                    </div>
                    <div class="col-6 col-md-2 px-2">
                        <input type="text" name="items[${rowIdx}][label]" class="form-control text-uppercase alert-warning" placeholder="Label Number">
                    </div>
                    <div class="col-6 col-md-2 px-2">
                        <select name="items[${rowIdx}][condition]" class="form-control select2 alert-warning text-uppercase" required>
                            <option value="" disabled selected>CONDITION*</option>
                            <option value="good">GOOD</option>
                            <option value="broken">BROKEN</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-1 px-2">
                        <input type="number" name="items[${rowIdx}][qty]" class="form-control text-uppercase alert-warning" placeholder="Qty*" required>
                    </div>
                    <div class="col-6 col-md-2 px-2">
                        <input type="text" name="items[${rowIdx}][uom]" class="form-control text-uppercase alert-warning" placeholder="UOM*" required>
                    </div>
                    <div class="col-12 col-md-1 px-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-label-danger btn-sm deleteRow w-100"><i class="fas fa-trash-alt me-1"></i> Delete</button>
                    </div>
                </div>
            `;

        container.appendChild(newRow);
        initSelect2(newRow);
        rowIdx++;
    });

    // Delete Row
    document
        .getElementById("items_container")
        .addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("deleteRow")) {
                let rows = document.querySelectorAll(
                    "#items_container .item-row"
                );
                if (rows.length > 1) {
                    e.target.closest(".item-row").remove();
                } else {
                    $.notify(
                        {
                            icon: "icon-bell",
                            title: "Warning",
                            message: "There must be at least 1 item!",
                        },
                        {
                            type: "warning",
                            placement: {
                                from: "top",
                                align: "right",
                            },
                            delay: 1000,
                        }
                    );
                }
            }
        });

    const config = {
        handling_level: {
            1: "selectgroup-success",
            2: "selectgroup-danger",
        },
        // shipment_by: {
        //     1: "selectgroup-primary",
        //     2: "selectgroup-warning",
        //     3: "selectgroup-secondary",
        // },
    };

    // Untuk setiap konfigurasi di atas
    Object.keys(config).forEach((name) => {
        const radios = document.querySelectorAll(`input[name="${name}"]`);
        const group = radios[0]?.closest(".selectgroup");

        if (!group) return;

        radios.forEach((radio) => {
            radio.addEventListener("change", function () {
                // Hapus semua class warna terdahulu
                group.classList.remove(
                    "selectgroup-primary",
                    "selectgroup-success",
                    "selectgroup-danger",
                    "selectgroup-warning",
                    "selectgroup-secondary"
                );

                // Tambahkan class baru sesuai value
                const colorClass = config[name][this.value];
                if (colorClass) group.classList.add(colorClass);
            });
        });

        // Jalankan saat load jika ada yang sudah terpilih (edit mode)
        const checked = document.querySelector(`input[name="${name}"]:checked`);
        if (checked) checked.dispatchEvent(new Event("change"));
    });
});

function initSelect2(context = document) {
    $(context)
        .find(".select2")
        .each(function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                $(this).select2({
                    dropdownParent: $(this).parent(),
                });
            }
        });
}

// ==================== VALIDATION FUNCTION ====================
function validateShipmentForm(showError = true) {
    // Reset dulu
    $(".error-message").remove();
    $(".field-error").removeClass("field-error");
    $(".select2-selection").removeClass("field-error");

    $("#createShipment [required]").each(function () {
        let value = $(this).val();

        if (!value || value.trim() === "") {
            if (showError) {
                if ($(this).hasClass("select2-hidden-accessible")) {
                    // --- kalau ini select2 ---
                    $(this)
                        .next(".select2")
                        .find(".select2-selection")
                        .addClass("field-error");

                    $(this)
                        .next(".select2")
                        .after(
                            '<div class="error-message text-danger mt-1">This field is required</div>'
                        );
                } else {
                    // --- kalau input biasa ---
                    $(this).addClass("field-error");
                    $(this).after(
                        '<div class="error-message text-danger mt-1">This field is required</div>'
                    );
                }
            }
        }
    });

    // --- Validasi radio button ---
    validateRadioGroup("is_asset", showError);
    validateRadioGroup("shipment_by", showError);
    validateRadioGroup("handling_level", showError);
}

function validateRadioGroup(groupName, showError = true) {
    const radios = $(`input[name='${groupName}']`);
    if (radios.length > 0 && radios.filter(":checked").length === 0) {
        if (showError) {
            const group = radios.closest(".selectgroup");
            group.addClass("field-error");
            group.after(
                '<div class="error-message text-danger mt-1">Please select one option</div>'
            );
        }
    }
}

// ==================== SUBMIT HANDLER ====================
$("#createShipment").on("submit", function (e) {
    e.preventDefault();
    const form = this;

    const submitForm = () => {
        validateShipmentForm(true);

        // --- kalau ada error stop dulu ---
        if ($(".error-message").length > 0) {
            $("html, body").animate(
                { scrollTop: $(".error-message").first().offset().top - 100 },
                500
            );
            return;
        }

        // --- Validasi tambahan ---
        const origin = $("#sender").val()?.trim();
        const destination = $("#destination").val()?.trim();
        if (origin && destination && origin === destination) {
            $.notify(
                {
                    icon: "icon-bell",
                    title: "Shipment Failed!",
                    message: "Sender and destination cannot be the same.",
                },
                {
                    type: "danger",
                    placement: { from: "top", align: "right" },
                    delay: 3000,
                }
            );

            $("#destination")
                .next(".select2")
                .find(".select2-selection")
                .addClass("field-error");
            return;
        }

        // --- Validasi minimal 1 item ---
        if ($("#items_container .item-row").length === 0) {
            $.notify(
                {
                    icon: "icon-bell",
                    title: "Shipment Failed!",
                    message: "At least one item is required.",
                },
                {
                    type: "danger",
                    placement: { from: "top", align: "right" },
                    delay: 3000,
                }
            );
            return;
        }

        // --- kalau semua valid ---
        form.submit();
    };

    submitForm();
});

// ==================== FIELD CHANGE HANDLER ====================
$(document).on(
    "change input",
    "#createShipment [required], .select2-hidden-accessible, input[name='is_asset'], input[name='shipment_by'], input[name='handling_level']",
    function () {
        const $field = $(this);
        const name = $field.attr("name");

        // --- Hapus error ---
        if ($field.hasClass("select2-hidden-accessible")) {
            $field
                .next(".select2")
                .find(".select2-selection")
                .removeClass("field-error");
            $field.next(".select2").next(".error-message").remove();
        } else if ($field.attr("type") === "radio") {
            // hapus error untuk semua radio dalam satu group
            $(`input[name='${name}']`)
                .closest(".selectgroup")
                .removeClass("field-error")
                .next(".error-message")
                .remove();
        } else {
            $field.removeClass("field-error");
            $field.next(".error-message").remove();
        }

        // --- Jalankan validasi ulang ---
        if ($field.hasClass("select2-hidden-accessible")) {
            if (!$field.val()) {
                $field
                    .next(".select2")
                    .find(".select2-selection")
                    .addClass("field-error");
                $field
                    .next(".select2")
                    .after(
                        '<div class="error-message text-danger mt-1">This field is required</div>'
                    );
            }
        } else if ($field.attr("type") === "radio") {
            // validasi: apakah salah satu radio dalam group sudah dipilih
            if (!$(`input[name='${name}']:checked`).length) {
                $(`input[name='${name}']`)
                    .closest(".selectgroup")
                    .addClass("field-error")
                    .after(
                        '<div class="error-message text-danger mt-1">This field is required</div>'
                    );
            }
        } else {
            if (!$field.val() || $field.val().trim() === "") {
                $field.addClass("field-error");
                $field.after(
                    '<div class="error-message text-danger mt-1">This field is required</div>'
                );
            }
        }
    }
);
