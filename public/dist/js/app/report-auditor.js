$(function () {
    let table;

    // Helper untuk bangun query string dari filter
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

        // Sembunyikan dulu tabel + tombol export
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
            table = $("#report-auditor-datatables").DataTable({
                processing: false,
                paging: false,
                searching: false,
                ajax: {
                    url: "/ajax/list-report-auditor",
                    data: function (d) {
                        // Ambil filter terbaru
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
                    { data: "auditor" },
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
                    { data: "wilayah" },
                    { data: "regional" },
                    { data: "area" },
                ],
                order: [
                    [3, "asc"], // wilayah
                    [0, "asc"], // auditor
                ],
                rowCallback: function (row, data) {
                    $(row).css("cursor", "pointer");
                    $(row)
                        .off("click")
                        .on("click", function () {
                            let url =
                                "/report-auditors/details/" + data.auditor_id;
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
        let url = "/report-auditors/export";
        let query = buildParams();
        if (query) url += "?" + query;
        window.location.href = url;
    });
});
