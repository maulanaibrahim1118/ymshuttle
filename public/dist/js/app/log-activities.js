$(function () {
    function initDataTable() {
        return $("#log-datatables").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: listUrl,
                data: function (d) {
                    d.subject = $("#subject").val();
                    d.username = $("#username").val();
                },
            },
            columns: [
                { data: "created_at", name: "created_at" },
                { data: "created_by", name: "created_by" },
                { data: "subject", name: "subject" },
                { data: "description", name: "description" },
                { data: "error", name: "error" },
                { data: "ip_address", name: "ip_address" },
                { data: "agent", name: "agent" },
                { data: "url", name: "url" },
            ],
            order: [[0, "desc"]],
        });
    }

    let table = initDataTable();

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $(document).on("click", ".view-error", function (e) {
        e.preventDefault();
        const errorText = $(this).data("error");
        $("#errorDetail").html(errorText);
    });
});
