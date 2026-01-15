let page = 1;
let loading = false;
let finished = false;
let currentSearch = "";
let debounceTimer;

function renderCard(item) {
    const detailUrl = `/shipments/details/${item.encrypted_id}`;
    let badgeHtml = "";

    if (item.status == "1") {
        badgeHtml = `<span class="badge bg-info"><i class="fas fa-box"></i> Created</span>`;
    } else if (item.status == "2") {
        badgeHtml = `<span class="badge bg-secondary"><i class="fas fa-truck-loading"></i> On Loading</span>`;
    } else if (item.status == "3") {
        badgeHtml = `<span class="badge bg-warning text-light"><i class="fas fa-shipping-fast"></i> On Delivery</span>`;
    } else if (item.status == "4") {
        badgeHtml = `<span class="badge bg-primary"><i class="fas fa-people-carry"></i> Delivered</span>`;
    } else if (item.status == "5") {
        badgeHtml = `<span class="badge bg-success"><i class="fas fa-box-open"></i> Received</span>`;
    } else {
        badgeHtml = `<span class="badge bg-danger"><i class="fas fa-times"></i> Cancelled</span>`;
    }

    return `
        <div class="col-md-6 col-lg-6 col-xxl-4 px-2 mb-3">
            <a href="${detailUrl}" class="text-decoration-none text-reset d-block">
                <div class="card shipment-card ripple-card mb-0 shadow-sm position-relative py-2 px-3 h-100">
                    <div class="card-body d-flex align-items-center">

                        <div style="flex: 1 1 auto; min-width: 0; padding-right: 8px; overflow: hidden;">
                            <h5 class="shipment-store-card text-uppercase text-truncate mb-2 fw-bolder">
                                ${item.no_shipment || "Unknown"} 
                                <span class="fw-normal text-muted"> | ${
                                    item.category?.name || "Unknown"
                                }</span>
                            </h5>

                            <h5 class="shipment-store-card text-muted mb-0 text-uppercase text-truncate">
                                <i class="fas fa-dot-circle text-primary me-1"></i>
                                <span class="text-dark">
                                    ${
                                        item.sender_location?.clean_name ||
                                        "Unknown"
                                    }
                                </span>
                            </h5>

                            <h5 class="shipment-store-card text-muted mb-0 text-uppercase text-truncate">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <span class="fw-bolder text-dark">
                                    ${
                                        item.receiver_location?.clean_name ||
                                        "Unknown"
                                    }
                                </span>
                            </h5>
                        </div>

                        <div class="flex-shrink-0">
                            ${badgeHtml}
                            <span class="chevron-move ms-3 me-2">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>`;
}

// helper buat key tanggal lokal (YYYY-MM-DD)
function localDateKey(dateInput) {
    const d = new Date(dateInput);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${dd}`;
}

function getFilterData() {
    return {
        category: $("#category").val(),
        sender: $("#sender").val(),
        destination: $("#destination").val(),
        date: $("#date").val(),
    };
}

function loadShipment(append = true) {
    if (loading || finished) return;

    loading = true;
    $("#loading").show();

    const searchVal = $("#searchInput").val().trim();
    currentSearch = searchVal;

    $.ajax({
        url: "/ajax/list-deliveries",
        data: {
            page: page,
            search: searchVal,
            ...getFilterData(),
        },
        success: function (res) {
            if (res.data.length > 0) {
                const groups = {};
                const order = [];

                // hari ini & kemarin (lokal)
                const todayKey = localDateKey(new Date());
                const yesterdayDate = new Date();
                yesterdayDate.setDate(yesterdayDate.getDate() - 1);
                const yesterdayKey = localDateKey(yesterdayDate);

                res.data.forEach((item) => {
                    const groupKey = localDateKey(item.ledger_created_at);
                    if (!groups[groupKey]) {
                        groups[groupKey] = [];
                        order.push(groupKey);
                    }
                    groups[groupKey].push(item);
                });

                let batchHtml = "";

                order.forEach((groupKey) => {
                    const items = groups[groupKey];
                    const firstDateObj = new Date(items[0].ledger_created_at);

                    let dateLabel;
                    if (groupKey === todayKey) dateLabel = "Today";
                    else if (groupKey === yesterdayKey) dateLabel = "Yesterday";
                    else dateLabel = firstDateObj.toLocaleDateString("id-ID");

                    const $existingHeader = $(
                        `.break-date[data-date="${groupKey}"]`
                    );

                    if (append && $existingHeader.length) {
                        const $existingRow = $existingHeader.next(".row");
                        const existingCountBefore =
                            $existingRow.children(".col-md-6").length;

                        let newCardsHtml = "";
                        items.forEach((it) => {
                            newCardsHtml += renderCard(it);
                        });

                        $existingRow.append(newCardsHtml);

                        const total = existingCountBefore + items.length;
                        let $totalSpan =
                            $existingHeader.find("span.date-total");
                        if ($totalSpan.length) {
                            $totalSpan.text(`(${total})`);
                        } else {
                            $existingHeader
                                .find("h7")
                                .append(
                                    ` <span class="text-muted date-total">(${total})</span>`
                                );
                        }
                    } else {
                        batchHtml += `
                            <div class="break-date w-100 ps-2 mt-4 mb-3" data-date="${groupKey}">
                                <h7 class="fw-bolder text-dark pb-1">
                                    <i class="fas fa-calendar-alt me-2"></i>${dateLabel}
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

                if (append) {
                    if (batchHtml) $("#shipmentCards").append(batchHtml);
                } else {
                    $("#shipmentCards").html(batchHtml);
                    page = 1;
                    finished = false;
                }

                if (res.next_page_url) {
                    page++;
                    $("#scrollSentinel").html("");
                } else {
                    finished = true;
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">All shipment deliveries have been shown.</p>'
                    );
                }
            } else {
                if (!append) {
                    $("#shipmentCards").html("");
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">Shipment delivery not found.</p>'
                    );
                } else {
                    finished = true;
                    $("#scrollSentinel").html(
                        '<p class="text-muted mt-3">No shipment delivery available.</p>'
                    );
                }
            }

            $("#searchShipment").show();
            $("#reloadShipment").show();
            $("#totalData").text(
                `Total shipment delivery(s): ${res.total ?? 0} ${
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
                loadShipment(true);
            }
        },
        { threshold: 1 }
    );

    observer.observe(sentinel);
}

$(function () {
    loadShipment();
    setupInfiniteScroll();

    $("#searchInput").on("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            page = 1;
            finished = false;

            $("#scrollSentinel").empty();
            loadShipment(false);
        }, 500);
    });

    $("#filter-form").on("submit", function (e) {
        e.preventDefault();
        page = 1;
        loading = false;
        finished = false;

        $("#shipmentCards").empty();
        $("#scrollSentinel").empty();
        $("#loading").show();
        loadShipment(false);
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

    $(document).on("click", "#reloadShipment", function (e) {
        e.preventDefault();
        page = 1;
        loading = false;
        finished = false;

        $("#shipmentCards").empty();
        $("#scrollSentinel").empty();
        $("#loading").show();
        loadShipment(false);
    });

    flatpickr("#date", {
        dateFormat: "d/m/Y",
        allowInput: true,
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

document.getElementById("backToTop").addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const backToTopBtn = document.getElementById("backToTop");
    const addBtn = document.querySelector(".floating-add-button");

    function setBackToTopPosition() {
        const isMobile = window.innerWidth < 991;

        if (isMobile) {
            if (addBtn) {
                backToTopBtn.style.bottom = "180px";
            }
        } else {
            if (addBtn) {
                backToTopBtn.style.bottom = "110px";
            }
        }
    }

    setBackToTopPosition();

    // kalau user resize window, update posisi
    window.addEventListener("resize", setBackToTopPosition);
});

document.addEventListener("click", function (e) {
    const card = e.target.closest(".ripple-card");
    if (!card) return;

    const rect = card.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);

    card.style.setProperty("--ripple-size", `${size}px`);

    const ripple = document.createElement("span");
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
    ripple.classList.add("ripple-effect");

    card.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
});
