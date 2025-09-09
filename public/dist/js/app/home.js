function getRegionalLabel() {
    let selected = $("#regionalFilter option:selected").text();
    return selected && selected !== "ALL REGIONALS"
        ? `[${selected}]`
        : "[ALL REGIONALS]";
}

function getAuditorMedal(index) {
    if (index === 0) return " 🥇";
    if (index === 1) return " 🥈";
    if (index === 2) return " 🥉";
    return "";
}

function getStoreTrophy(index) {
    if (index === 0) return " 🏆";
    if (index === 1) return " 🏆";
    if (index === 2) return " 🏆";
    return "";
}

function fetchTopPerformers(month = "", year = "", regional = "") {
    let auditorList = $("#auditorList");
    let storeList = $("#storeList");

    // sembunyikan list, tampilkan loader
    auditorList.hide();
    storeList.hide();
    $("#auditorLoading").show();
    $("#storeLoading").show();

    $.get("/home/top-performers", { month, year, regional })
        .done(function (res) {
            let periodLabel = "";
            if (month && year)
                periodLabel = `[${$(
                    "#monthFilter option:selected"
                ).text()} ${year}]`;
            else if (month)
                periodLabel = `[${$(
                    "#monthFilter option:selected"
                ).text()} ${new Date().getFullYear()}]`;
            else if (year) periodLabel = `[${year}]`;
            else periodLabel = "[ALL TIMES]";

            $("#auditorPeriod").text(getRegionalLabel() + " - " + periodLabel);
            $("#storePeriod").text(getRegionalLabel() + " - " + periodLabel);

            auditorList.empty();
            storeList.empty();

            // === Auditors ===
            if (res.auditors.length === 0) {
                auditorList.append(
                    `<p class="list-group-item text-center text-muted pt-3 mb-0">No checklist data available</p>`
                );
            } else {
                res.auditors.forEach((a, index) => {
                    auditorList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="mx-2">
                                <strong>${index + 1}.</strong> 
                                <span class="ms-1">${a.auditor.toUpperCase()}${getAuditorMedal(
                        index
                    )}</span><br>
                                <small class="text-muted">${a.wilayah}</small>
                            </div>
                            <span class="fw-bold">${parseFloat(
                                a.avg_percentage
                            ).toFixed(2)}%</span>
                        </li>
                    `);
                });
            }

            // === Stores ===
            if (res.stores.length === 0) {
                storeList.append(
                    `<p class="list-group-item text-center text-muted pt-3 mb-0">No checklist data available</p>`
                );
            } else {
                res.stores.forEach((s, index) => {
                    storeList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="mx-2">
                                <strong>${index + 1}.</strong> 
                                <span class="ms-1">${s.store_name.toUpperCase()}${getStoreTrophy(
                        index
                    )}</span><br>
                                <small class="text-muted">${
                                    s.korwil_name
                                }</small>
                            </div>
                            <span class="fw-bold">${parseFloat(
                                s.avg_percentage
                            ).toFixed(2)}%</span>
                        </li>
                    `);
                });
            }
        })
        .fail(function () {
            auditorList.html(
                `<p class="list-group-item text-center text-danger pt-3">Failed to load auditors</p>`
            );
            storeList.html(
                `<p class="list-group-item text-center text-danger pt-3">Failed to load stores</p>`
            );
        })
        .always(function () {
            // sembunyikan loader, tampilkan list
            $("#auditorLoading").hide();
            $("#storeLoading").hide();
            auditorList.show();
            storeList.show();
        });
}

$(function () {
    fetchTopPerformers(); // default: all time

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();
        let month = $("#monthFilter").val();
        let year = $("#yearFilter").val();
        let regional = $("#regionalFilter").val();
        fetchTopPerformers(month, year, regional);
    });
});
