$(function () {
    let table;

    function buildParams() {
        let params = [];
        ["store", "month", "year"].forEach(function (f) {
            let v = $("#" + f).val();
            if (v) params.push(f + "=" + encodeURIComponent(v));
        });
        return params.join("&");
    }

    function validateStore() {
        let $store = $("#store");
        let $select2 = $store.next(".select2"); // wrapper select2
        let $error = $("#store-error");

        if (!$store.val()) {
            // tambahkan border merah ke select2
            $select2.find(".select2-selection").addClass("is-invalid");

            if ($error.length === 0) {
                $select2.after(
                    "<div id='store-error' class='invalid-feedback d-block'>Choose a store!</div>"
                );
            }
            return false;
        } else {
            // hapus border merah
            $select2.find(".select2-selection").removeClass("is-invalid");
            $("#store-error").remove();
            return true;
        }
    }

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();

        // ✅ validasi store wajib
        if (!validateStore()) return;

        $("#tableWrapper").attr("hidden", true);
        $("#collapseTwo").addClass("show");
        $("#loading").show();

        let storeText = $("#store option:selected").text();
        let monthVal = $("#month").val();
        let yearVal = $("#year").val();

        let filterLabel = "";
        if ($("#store").val()) {
            filterLabel += "<i class='fas fa-store me-2'></i>" + storeText;
        }
        $("#filter-info").html(filterLabel);

        if (table) {
            table.ajax.reload(function () {
                $("#loading").hide();
                $("#tableWrapper").removeAttr("hidden");
            }, false);
        } else {
            table = $("#report-criteria-datatables").DataTable({
                processing: false,
                paging: false,
                searching: false,
                ajax: {
                    url: "/ajax/list-report-criteria",
                    data: function (d) {
                        d.store = $("#store").val();
                        d.month = monthVal;
                        d.year = yearVal;
                    },
                    dataSrc: function (json) {
                        $("#loading").hide();
                        $("#tableWrapper").removeAttr("hidden");
                        return json.data;
                    },
                    error: function () {
                        $("#loading").hide();
                    },
                },
                columns: [
                    { data: "criteria_name", className: "text-wrap" },
                    { data: "criteria_desc", className: "text-wrap" },
                    { data: "good_count" },
                    { data: "not_good_count" },
                    { data: "none_count" },
                ],
                order: [[0, "asc"]],
            });
        }
    });

    $("#exportExcel").on("click", function (e) {
        e.preventDefault();

        // ✅ validasi store wajib
        if (!validateStore()) return;

        let url = "/report-criterias/export";
        let query = buildParams();
        if (query) url += "?" + query;
        window.location.href = url;
    });
});
