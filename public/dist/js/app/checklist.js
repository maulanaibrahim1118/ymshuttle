let page = 1;
let loading = false;
let finished = false;
let currentSearch = "";
let debounceTimer;

function renderCard(item) {
    const detailUrl = `/checklists/details/${item.encrypted_id}`;
    const createdAt = new Date(item.created_at).toLocaleDateString("id-ID");

    let badgeHtml = "";

    if (item.status == "0") {
        badgeHtml = `<span class="position-absolute top-0 end-0 m-3 badge bg-info"><i class="fas fa-hourglass-half"></i></span>`;
    } else {
        const percentage = item.percentage || 0;
        let badgeClass = "bg-danger";
        if (percentage > 75) {
            badgeClass = "bg-success";
        } else if (percentage > 50) {
            badgeClass = "bg-warning text-light";
        }
        badgeHtml = `<span class="position-absolute top-0 end-0 m-3 badge ${badgeClass}">${percentage}<i class='fas fa-percentage ms-1'></i></span>`;
    }

    return `
        <div class="col-md-6 col-lg-4 px-2 mb-3">
            <div class="card alert-warning mb-0 shadow-sm position-relative">
                <div class="card-body d-flex justify-content-between align-items-start">
                    ${badgeHtml}
                    <div>
                        <h5 class="checklist-store-card text-uppercase fw-bolder text-truncate">
                            <i class="fas fa-store me-1"></i>
                            <a href="#">
                                ${item.store.site || "Unknown Site"} -
                                ${item.store.clean_name || "Unknown Store"}
                            </a>
                        </h5>
                        <p class="checklist-store-card-sub text-muted mb-0 text-uppercase">
                            <i class="fas fa-check-circle me-1"></i>
                            <span class="fw-bolder text-warning">${
                                item.no_checklist || ""
                            }</span> | ${item.category.name || ""}
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center small">
                    <span class="text-muted text-capitalize text-truncate">
                        <i class="fas fa-calendar-alt me-1"></i>${createdAt}
                        <i class="fas fa-user-circle ms-2 me-1"></i>${
                            item.creator?.name || "Unknown"
                        }
                    </span>
                    <a href="${detailUrl}" class="text-decoration-none text-info"><i class="fas fa-file-alt me-1"></i>Details<i class="fas fa-angle-right ms-1"></i></a>
                </div>
            </div>
        </div>`;
}

function getFilterData() {
    return {
        store: $("#store").val(),
        wilayah: $("#wilayah").val(),
        regional: $("#regional").val(),
        area: $("#area").val(),
        status: $("#status").val(),
        city: $("#city").val(),
        category: $("#category").val(),
        auditor: $("#auditor").val(),
        period: $("#period").val(),
    };
}

function loadChecklist(append = true) {
    if (loading || finished) return;

    loading = true;
    $("#loading").show();

    const searchVal = $("#searchInput").val().trim();
    currentSearch = searchVal;

    // Ambil lastDate dan lastRow dari DOM jika append (tetap dipertahankan, tidak dipakai untuk total)
    let lastDate = null;
    let lastRow = null;
    if (append && $("#checklistCards .break-date").length > 0) {
        lastDate = $("#checklistCards .break-date")
            .last()
            .find("h7")
            .text()
            .trim();
        lastRow = $("#checklistCards .break-date").last().next(".row");
    }

    $.ajax({
        url: "/ajax/list-checklists",
        data: {
            page: page,
            search: searchVal,
            ...getFilterData(),
        },
        success: function (res) {
            let html = "";

            if (res.data.length > 0) {
                // --- group per tanggal, sambil mempertahankan urutan kemunculan ---
                const groups = {};
                const order = [];
                res.data.forEach((item) => {
                    const groupKey = new Date(item.created_at)
                        .toISOString()
                        .split("T")[0]; // YYYY-MM-DD
                    if (!groups[groupKey]) {
                        groups[groupKey] = [];
                        order.push(groupKey);
                    }
                    groups[groupKey].push(item);
                });

                // referensi today/yesterday (hitung 1x)
                const today = new Date();
                const yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);

                // batchHtml untuk grup baru (yang akan kita append sekaligus)
                let batchHtml = "";

                // proses per grup sesuai urutan
                order.forEach((groupKey) => {
                    const items = groups[groupKey];
                    const firstDateObj = new Date(items[0].created_at);
                    let dateLabel = firstDateObj.toLocaleDateString("id-ID");
                    if (firstDateObj.toDateString() === today.toDateString())
                        dateLabel = "Today";
                    else if (
                        firstDateObj.toDateString() === yesterday.toDateString()
                    )
                        dateLabel = "Yesterday";

                    // cek apakah header untuk groupKey sudah ada di DOM
                    const $existingHeader = $(
                        `.break-date[data-date="${groupKey}"]`
                    );

                    if (append && $existingHeader.length) {
                        // Grup sudah ada: ambil row yang ada dan hitung existing count
                        const $existingRow = $existingHeader.next(".row");
                        const existingCountBefore =
                            $existingRow.children(".col-md-6").length;

                        // buat HTML card baru untuk grup ini
                        let newCardsHtml = "";
                        items.forEach((it) => {
                            newCardsHtml += renderCard(it);
                        });

                        // append card ke row yang sudah ada
                        $existingRow.append(newCardsHtml);

                        // update total di header (gunakan atau buat span.date-total)
                        const total = existingCountBefore + items.length;
                        let $totalSpan =
                            $existingHeader.find("span.date-total");
                        if ($totalSpan.length) {
                            $totalSpan.text(`(${total})`);
                        } else {
                            // tambahkan span jika belum ada
                            $existingHeader
                                .find("h7")
                                .append(
                                    ` <span class="text-muted date-total">(${total})</span>`
                                );
                        }
                    } else {
                        // Grup belum ada: buat header + row di batchHtml (total = jumlah item pada batch ini)
                        batchHtml += `
                            <div class="break-date w-100 ps-2 mt-4 mb-3" data-date="${groupKey}">
                                <h7 class="fw-bolder text-dark pb-1">
                                    <i class="fas fa-ellipsis-v me-2"></i>${dateLabel}
                                    <span class="text-muted date-total">(${items.length})</span>
                                </h7>
                            </div>
                            <div class="row px-0 ms-0">
                        `;
                        items.forEach((it) => {
                            batchHtml += renderCard(it);
                        });
                        batchHtml += `</div>`;
                    }
                });

                // append batchHtml sekaligus (untuk grup-grup baru)
                if (append) {
                    if (batchHtml) $("#checklistCards").append(batchHtml);
                } else {
                    // replace seluruh konten
                    $("#checklistCards").html(batchHtml);
                    page = 1;
                    finished = false;
                }

                if (res.next_page_url) {
                    page++;
                    $("#scrollSentinel").html("");
                } else {
                    finished = true;
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">All checklists have been shown.</p>'
                    );
                }
            } else {
                if (!append) {
                    $("#checklistCards").html("");
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">Checklist not found.</p>'
                    );
                } else {
                    finished = true;
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">No checklist available. Please create a new one.</p>'
                    );
                }
            }

            $("#searchChecklist").show();
            $("#reloadChecklist").show();
            $("#totalData").text(
                `Total checklist(s): ${res.total ?? 0} ${
                    res.total >= 12 ? "(load per 12 rows)" : ""
                }`
            );

            loading = false;
            $("#loading").hide();
        },
        error: function () {
            loading = false;
            $("#loading").hide();
        },
    });
}

function setupInfiniteScroll() {
    let sentinel = document.querySelector("#scrollSentinel");

    let observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !loading && !finished) {
                loadChecklist(true);
            }
        },
        { threshold: 1 }
    );

    observer.observe(sentinel);
}

$(function () {
    loadChecklist();
    setupInfiniteScroll();

    $("#searchInput").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            page = 1;
            finished = false;

            $("#scrollSentinel").empty();
            loadChecklist(false);
        }, 500);
    });

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();
        page = 1;
        loading = false;
        finished = false;

        $("#checklistCards").empty();
        $("#scrollSentinel").empty();
        $("#loading").show();
        loadChecklist(false);
    });

    $("#category_id").on("change", function () {
        let categoryId = $(this).val();
        $("#criteriaList").empty();
        $("#criteria-container").addClass("d-none");

        if (!categoryId) return;

        $.get(
            `/ajax/get-criterias-by-category/${categoryId}`,
            function (criterias) {
                if (criterias.length === 0) return;

                criterias.forEach(function (criteria, index) {
                    $("#criteriaList").append(`
                    <div class="fw-bold">${
                        index + 1
                    }. ${criteria.name.toUpperCase()}</div>
                    <div class="text-capitalize text-muted mb-2">${
                        criteria.description
                    }</div>
                `);
                });

                $("#criteria-container").removeClass("d-none");
            }
        );
    });

    $(document).on("click", "#reloadChecklist", function (e) {
        e.preventDefault();
        page = 1;
        loading = false;
        finished = false;

        $("#checklistCards").empty();
        $("#scrollSentinel").empty();
        $("#loading").show();
        loadChecklist(false); // ganti konten lama, bukan append
    });
});

window.addEventListener("scroll", function () {
    const backToTop = document.getElementById("backToTop");
    if (window.scrollY > 300) {
        backToTop.style.display = "inline-block";
    } else {
        backToTop.style.display = "none";
    }
});

// Scroll ke atas dengan animasi saat tombol diklik
document.getElementById("backToTop").addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const backToTopBtn = document.getElementById("backToTop");
    const addBtn = document.querySelector(".floating-add-button");

    if (addBtn) {
        // Jika tombol Add ada, naikkan posisi back to top
        backToTopBtn.style.bottom = "100px";
    } else {
        // Jika tidak ada, posisikan lebih bawah (default posisi Add)
        backToTopBtn.style.bottom = "30px";
    }
});
