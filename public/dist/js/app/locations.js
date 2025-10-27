$(function () {
    function initDataTable() {
        return $("#location-datatables").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/ajax/list-locations",
                data: function (d) {
                    d.id = $("#location_id").val();
                    d.city = $("#city").val();
                    d.active = $("#active").val();
                    d.support = $("#support").val();
                },
            },
            columns: [
                { data: "name", name: "name" },
                { data: "address", name: "address" },
                { data: "city", name: "city" },
                { data: "email", name: "email" },
                { data: "dc_support", name: "dc_support" },
                { data: "telp", name: "telp" },
                { data: "is_active", name: "is_active" },
                { data: "created_by", name: "created_by" },
                { data: "created_at", name: "created_at" },
                { data: "updated_by", name: "updated_by" },
                { data: "updated_at", name: "updated_at" },
            ],
            order: [
                [6, "desc"],
                [0, "asc"],
            ],
            columnDefs: [
                {
                    targets: "_all",
                    createdCell: function (td) {
                        td.classList.add("text-uppercase");
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        const statusMap = {
                            1: { label: "ACTIVE", class: "badge-success" },
                            0: { label: "INACTIVE", class: "badge-danger" },
                        };

                        if (type === "display") {
                            const s = statusMap[data];
                            return s
                                ? `<span class="badge ${s.class}">${s.label}</span>`
                                : `<span class="badge badge-secondary">UNKNOWN</span>`;
                        }

                        return data; // untuk sort & filter tetap angka
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
});
