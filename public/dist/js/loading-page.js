// LOAD REQUEST
const overlay = document.getElementById("loadingOverlay");

// Muncul saat mulai request (form submit, klik link, reload)
window.addEventListener("beforeunload", function () {
    // Jangan tampilkan overlay jika tombol export diklik
    if (document.activeElement && document.activeElement.id === "exportExcel") {
        return; // skip
    }
    overlay.style.display = "flex";
    document.querySelectorAll("button, a, label").forEach((el) => {
        el.setAttribute("disabled", true);
        el.classList.add("disabled");
        el.style.pointerEvents = "none"; // untuk <a> agar benar-benar tidak bisa diklik
    });
});

// Hilang saat load selesai
window.addEventListener("load", function () {
    overlay.style.display = "none";
});

window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        // Halaman datang dari BFCache → paksa reload penuh
        window.location.reload();
    }
});

// LOAD ASSETS
document.addEventListener("DOMContentLoaded", function () {
    const loadingBar = document.getElementById("loadingBar");

    // Disable semua tombol/link saat loading
    const interactiveElements = document.querySelectorAll("button, a, label");
    interactiveElements.forEach((el) => {
        el.setAttribute("disabled", true);
        el.classList.add("disabled");
        el.style.pointerEvents = "none";
    });

    // Reset style dan tampilkan loading bar
    loadingBar.style.display = "block";
    loadingBar.style.width = "0";
    loadingBar.style.transition = "width 1s linear"; // durasi animasi bar

    // Trigger animasi bar
    requestAnimationFrame(() => {
        loadingBar.style.width = "100%";
    });

    // Setelah animasi selesai, sembunyikan bar dan enable tombol/link
    loadingBar.addEventListener("transitionend", () => {
        loadingBar.style.display = "none";

        interactiveElements.forEach((el) => {
            el.removeAttribute("disabled");
            el.classList.remove("disabled");
            el.style.pointerEvents = "";
        });
    });

    // Fallback jika halaman lambat load (misal gambar besar)
    window.onload = function () {
        // Pastikan lebar bar tetap 100%
        loadingBar.style.width = "100%";
    };
});
