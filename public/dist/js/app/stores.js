$(function () {
    function initDataTable() {
        let columns = [];

        // Tambahkan kolom Action jika user punya izin
        if (window.canAnyStoreAction) {
            columns.push({
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            });
        }

        // Kolom tetap
        columns = columns.concat([
            { data: "code", name: "code" },
            { data: "site", name: "site" },
            { data: "initial", name: "initial" },
            { data: "cluster", name: "clusters.name" },
            { data: "name", name: "name" },
            { data: "wilayah", name: "wilayahs.name" },
            { data: "korwil", name: "korwil" },
            { data: "regional", name: "regionals.name" },
            { data: "chief", name: "chief" },
            { data: "area", name: "areas.name" },
            { data: "manager", name: "manager" },
            { data: "address", name: "address" },
            { data: "city", name: "city" },
            { data: "opening_date", name: "opening_date" },
            { data: "email", name: "email" },
            { data: "dc_support", name: "dc_support" },
            { data: "telp", name: "telp" },
            { data: "status", name: "status" },
            { data: "created_by", name: "created_by" },
            { data: "created_at", name: "created_at" },
            { data: "updated_by", name: "updated_by" },
            { data: "updated_at", name: "updated_at" },
        ]);

        let orderBy = window.canAnyStoreAction
            ? [
                  [6, "asc"],
                  [5, "asc"],
              ]
            : [
                  [5, "asc"],
                  [4, "asc"],
              ];

        return $("#store-datatables").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/ajax/list-stores",
                data: function (d) {
                    d.id = $("#store_id").val();
                    d.wilayah_id = $("#wilayah").val();
                    d.cluster_id = $("#cluster").val();
                    d.area_id = $("#area").val();
                    d.regional_id = $("#regional").val();
                    d.city = $("#city").val();
                    d.homebase = $("#homebase").val();
                    d.active = $("#active").val();
                    d.support = $("#support").val();
                },
            },
            columns: columns,
            order: orderBy,
            columnDefs: [
                {
                    targets: "_all",
                    createdCell: function (td) {
                        td.classList.add("text-uppercase");
                    },
                },
            ],
        });
    }

    let table = initDataTable();

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    document
        .getElementById("import_file")
        .addEventListener("change", function () {
            // Submit form
            document.getElementById("importForm").submit();
        });
});

$("#exportExcel").on("click", function (e) {
    e.preventDefault();

    // Ambil semua filter dari form (atau bisa juga langsung dari datatables ajax data)
    let filters = {
        id: $("#store_id").val(),
        wilayah_id: $("#wilayah").val(),
        cluster_id: $("#cluster").val(),
        area_id: $("#area").val(),
        regional_id: $("#regional").val(),
        city: $("#city").val(),
        homebase: $("#homebase").val(),
        active: $("#active").val(),
        support: $("#support").val(),
    };

    // Bangun query string
    let queryString = $.param(filters);

    overlay.style.display = "none";

    // Redirect ke route export dengan filter
    window.location.href = `${exportUrl}?${queryString}`;
});
