$(function () {
    const storeId = $("#id").val();
    const debounceTimers = {};
    let hasChanged = false;

    const formState = {
        isSiteUnique: false,
        isInitialUnique: false,
        isClusterValid: false,
        isNameUnique: false,
        isWilayahValid: false,
        isSupportValid: false,
        isOpeningDateValid: false,
        isHomebaseValid: false,
        isEmailValid: false,
        isTelpValid: false,
        isCityValid: false,
        isAddressValid: false,
    };

    function filterNonNumericInput(input) {
        return input.replace(/[^0-9]/g, "");
    }

    function toggleSubmitButton() {
        const allValid = Object.values(formState).every((v) => v === true);
        $("#submitBtn").prop("disabled", !(allValid && hasChanged));
    }

    function debounce(key, callback, delay = 500) {
        clearTimeout(debounceTimers[key]);
        debounceTimers[key] = setTimeout(callback, delay);
    }

    function validateUniqueField({
        selector,
        url,
        paramKey,
        stateKey,
        errorClass,
        messageId,
        pattern = null,
    }) {
        const value = $(selector).val().trim();

        if (!value || (pattern && !pattern.test(value))) {
            $(messageId).hide();
            $(errorClass).removeClass("has-error");
            formState[stateKey] = false;
            toggleSubmitButton();
            return;
        }

        const payload = { [paramKey]: value, id: storeId };

        $.ajax({
            url,
            type: "GET",
            data: payload,
            success: function (response) {
                formState[stateKey] = response.unique;

                $(messageId).toggle(!response.unique);
                $(errorClass).toggleClass("has-error", !response.unique);
            },
            error: function () {
                $(messageId).text("Error checking uniqueness.").show();
                formState[stateKey] = false;
                $(errorClass).addClass("has-error");
            },
            complete: toggleSubmitButton,
        });
    }

    function handleDebouncedValidation(selector, options) {
        $(selector).on("input", () => {
            debounce(selector, () => validateUniqueField(options));
        });

        if ($(selector).val().trim() !== "") {
            validateUniqueField(options);
        }
    }

    handleDebouncedValidation("#site", {
        selector: "#site",
        url: "/ajax/check-unique-store-site",
        paramKey: "site",
        stateKey: "isSiteUnique",
        errorClass: ".unique-site",
        messageId: "#site-taken",
    });

    $("#site").on("input", function () {
        let siteVal = filterNonNumericInput($(this).val());
        $(this).val(siteVal);

        // Validasi: harus 5 digit angka
        if (/^\d{5}$/.test(siteVal)) {
            const last3 = siteVal.slice(-3);
            $("#code").val(last3).trigger("input");
        } else {
            $("#code").val("").trigger("input");
        }
    });

    handleDebouncedValidation("#initial", {
        selector: "#initial",
        url: "/ajax/check-unique-store-initial",
        paramKey: "initial",
        stateKey: "isInitialUnique",
        errorClass: ".unique-initial",
        messageId: "#initial-taken",
        pattern: /^[0-9a-zA-Z]{1,10}$/,
    });

    handleDebouncedValidation("#name", {
        selector: "#name",
        url: "/ajax/check-unique-store-name",
        paramKey: "name",
        stateKey: "isNameUnique",
        errorClass: ".unique-name",
        messageId: "#name-taken",
    });

    $("#cluster_id").on("change", () =>
        validateSelect("#cluster_id", "isClusterValid")
    );
    $("#wilayah_id").on("change", () => {
        validateSelect("#wilayah_id", "isWilayahValid");
        autofillRegionalAndArea();
    });
    $("#dc_support").on("change", () =>
        validateSelect("#dc_support", "isSupportValid")
    );
    $("#is_homebase").on("change", () =>
        validateSelect("#is_homebase", "isHomebaseValid")
    );
    $("#opening_date").on("change", () =>
        validateNonEmpty("#opening_date", "isOpeningDateValid")
    );
    $("#email").on("input", () => validateNonEmpty("#email", "isEmailValid"));
    $("#telp").on("input", function () {
        let telp = filterNonNumericInput($(this).val());
        $(this).val(telp);
        validateNonEmpty("#telp", "isTelpValid");
    });
    $("#city").on("change", () => validateSelect("#city", "isCityValid"));
    $("#address").on("input", () =>
        validateNonEmpty("#address", "isAddressValid")
    );

    function validateSelect(selector, stateKey) {
        formState[stateKey] = $(selector).val() !== "";
        toggleSubmitButton();
    }

    function validateNonEmpty(selector, stateKey) {
        formState[stateKey] = $(selector).val().trim() !== "";
        toggleSubmitButton();
    }

    function autofillRegionalAndArea() {
        const wilayahId = $("#wilayah_id").val();
        if (!wilayahId) return;

        $.ajax({
            url: `/ajax/get-regional-and-area/${wilayahId}`,
            type: "GET",
            success: function (data) {
                $("#regional")
                    .val(data.regional_name || "")
                    .trigger("change");
                $("#area")
                    .val(data.area_name || "")
                    .trigger("change");
            },
            error: function () {
                console.error("Gagal mengambil data regional/area.");
            },
        });
    }

    // Detect user change on any form input
    $("form :input").on("input change", function () {
        hasChanged = true;
        toggleSubmitButton();
    });

    // Initial validation only (without enabling submit button yet)
    function runInitialValidation() {
        const checks = [
            { selector: "#cluster_id", stateKey: "isClusterValid" },
            { selector: "#wilayah_id", stateKey: "isWilayahValid" },
            { selector: "#dc_support", stateKey: "isSupportValid" },
            { selector: "#is_homebase", stateKey: "isHomebaseValid" },
            { selector: "#opening_date", stateKey: "isOpeningDateValid" },
            { selector: "#email", stateKey: "isEmailValid" },
            { selector: "#telp", stateKey: "isTelpValid" },
            { selector: "#city", stateKey: "isCityValid" },
            { selector: "#address", stateKey: "isAddressValid" },
        ];

        checks.forEach(({ selector, stateKey }) => {
            const el = $(selector);
            const val = el.val();
            if (el.length && val !== null && val.toString().trim() !== "") {
                if (el.is("select")) {
                    formState[stateKey] = true;
                } else {
                    formState[stateKey] = true;
                }
            }
        });

        toggleSubmitButton();
    }

    runInitialValidation();
});
