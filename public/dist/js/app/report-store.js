$(function () {
    let table;

    // Helper untuk build query string dari filter
    function buildParams() {
        let params = [];
        ["wilayah", "regional", "area", "month", "year"].forEach(function (f) {
            let v = $("#" + f).val();
            if (v) params.push(f + "=" + encodeURIComponent(v));
        });
        return params.join("&");
    }

    // Submit filter
    $("#filter-form").on("submit", function (e) {
        e.preventDefault();

        // Sembunyikan tabel + tombol export
        $("#tableWrapper").attr("hidden", true);
        $("#collapseTwo").addClass("show");

        // Tampilkan loader
        $("#loading").show();

        if (table) {
            // Reload datatable
            table.ajax.reload(function () {
                $("#loading").hide();
                $("#tableWrapper").removeAttr("hidden");
            }, false);
        } else {
            // Init datatable pertama kali
            table = $("#report-store-datatables").DataTable({
                processing: false,
                paging: false,
                searching: false,
                ajax: {
                    url: "/ajax/list-report-store",
                    data: function (d) {
                        d.wilayah = $("#wilayah").val();
                        d.regional = $("#regional").val();
                        d.area = $("#area").val();
                        d.month = $("#month").val();
                        d.year = $("#year").val();
                    },
                    dataSrc: function (json) {
                        $("#loading").hide();
                        $("#tableWrapper").removeAttr("hidden");
                        $("#collapseTwo").addClass("show");
                        return json.data;
                    },
                    error: function () {
                        $("#loading").hide();
                    },
                },
                columns: [
                    { data: "store_site" },
                    { data: "store_name" },
                    { data: "number_of_visits" },
                    {
                        data: "avg_percentage",
                        render: function (data, type, row) {
                            if (!data) return "-";
                            return `<span class="badge ${data.class}">
                        ${data.label} ${data.icon}
                    </span>`;
                        },
                    },
                    { data: "korwil_name" },
                    { data: "wilayah" },
                    { data: "regional" },
                    { data: "area" },
                ],
                order: [
                    [5, "asc"], // wilayah
                    [1, "asc"], // store_name
                ],
                rowCallback: function (row, data) {
                    $(row).css("cursor", "pointer");
                    $(row)
                        .off("click")
                        .on("click", function () {
                            let url =
                                "/report-stores/details/" + data.store_code;
                            let query = buildParams();
                            if (query) url += "?" + query;
                            window.location.href = url;
                        });
                },
            });
        }
    });

    // Export Excel
    $("#exportExcel").on("click", function (e) {
        e.preventDefault();
        let url = "/report-stores/export";
        let query = buildParams();
        if (query) url += "?" + query;
        window.location.href = url;
    });
});
