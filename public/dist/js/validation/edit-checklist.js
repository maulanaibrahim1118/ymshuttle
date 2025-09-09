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
    const isMobile =
        /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(
            navigator.userAgent
        );

    if (!isMobile) {
        document
            .querySelectorAll('input[type="file"][name^="images"]')
            .forEach((input) => {
                // Disable secara teknis
                input.disabled = true;
                input.parentElement.style.pointerEvents = "auto"; // biar bisa detect click di parent
                input.parentElement.style.opacity = "0.5";
                input.parentElement.title = ""; // kosongin tooltip

                // Pas user klik → munculin notif
                input.parentElement.addEventListener("click", function (e) {
                    e.preventDefault();
                    $.notify(
                        {
                            icon: "icon-bell",
                            title: "Warning",
                            message: "Image capture only available on mobile!",
                        },
                        {
                            type: "warning",
                            placement: { from: "top", align: "right" },
                            delay: 3000,
                        }
                    );
                });
            });
    }

    getLocationAndUpdateFields();
    const backToTopBtn = document.getElementById("backToTop");

    backToTopBtn.style.bottom = "30px";
});

function getLocationAndUpdateFields(onSuccess, onError) {
    if (!navigator.geolocation) {
        if (onError) onError();
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            let lat = pos.coords.latitude;
            let lng = pos.coords.longitude;

            fetch("/ajax/encrypt-coordinates", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({ latitude: lat, longitude: lng }),
            })
                .then((res) => res.json())
                .then((data) => {
                    document.getElementById("latitude").value = data.latitude;
                    document.getElementById("longitude").value = data.longitude;
                    if (onSuccess) onSuccess();
                })
                .catch((err) => {
                    console.error("Encrypt Failed:", err);
                    if (onError) onError();
                });
        },
        function (err) {
            console.warn(`ERROR(${err.code}): ${err.message}`);
            if (onError) onError();
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function resizeImage(file, maxWidth = 1024, quality = 0.7) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const reader = new FileReader();

        reader.onload = (e) => {
            img.src = e.target.result;
        };

        img.onload = () => {
            const canvas = document.createElement("canvas");
            const scaleSize = maxWidth / img.width;
            canvas.width = maxWidth;
            canvas.height = img.height * scaleSize;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(
                (blob) => {
                    if (!blob) {
                        reject(new Error("Canvas is empty"));
                        return;
                    }
                    const resizedFile = new File([blob], file.name, {
                        type: "image/jpeg",
                        lastModified: Date.now(),
                    });
                    resolve(resizedFile);
                },
                "image/jpeg",
                quality
            );
        };

        img.onerror = (error) => reject(error);
        reader.onerror = (error) => reject(error);

        reader.readAsDataURL(file);
    });
}

// Modifikasi fungsi previewImage agar resize dulu
async function previewImage(event, previewId) {
    const input = event.target;
    if (!input.files || input.files.length === 0) return;

    const originalFile = input.files[0];

    try {
        // Resize gambar
        const resizedFile = await resizeImage(originalFile);

        // Ganti file input dengan file hasil resize
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(resizedFile);
        input.files = dataTransfer.files;

        // Preview image dengan yang sudah diresize
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(resizedFile);
    } catch (err) {
        console.error("Resize image failed:", err);
    }
}

function resizeCanvasForHiDPI(canvas) {
    const ratio = window.devicePixelRatio || 1;
    const style = getComputedStyle(canvas);

    // Dapatkan ukuran CSS canvas sebagai angka (px)
    const width = parseFloat(style.width);
    const height = parseFloat(style.height);

    canvas.width = width * ratio;
    canvas.height = height * ratio;

    const ctx = canvas.getContext("2d");
    ctx.scale(ratio, ratio);
}

document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("signature-pad");
    const container = canvas.parentElement;
    container.style.position = "relative";

    resizeCanvasForHiDPI(canvas);
    const ctx = canvas.getContext("2d");

    let placeholder = document.getElementById("signature-placeholder");
    if (!placeholder) {
        placeholder = document.createElement("div");
        placeholder.id = "signature-placeholder";
        placeholder.textContent = "Sign here";
        placeholder.style.position = "absolute";
        placeholder.style.top = "50%";
        placeholder.style.left = "50%";
        placeholder.style.transform = "translate(-50%, -50%)";
        placeholder.style.color = "rgba(0,0,0,0.3)";
        placeholder.style.fontSize = "18px";
        placeholder.style.userSelect = "none";
        placeholder.style.pointerEvents = "none";
        placeholder.style.zIndex = "10";
        container.appendChild(placeholder);
    }

    let drawing = false;

    ctx.lineWidth = 2;
    ctx.lineJoin = "round";
    ctx.lineCap = "round";
    ctx.strokeStyle = "#000";

    // devicePixelRatio dipakai untuk menyesuaikan koordinat input
    const ratio = window.devicePixelRatio || 1;

    const getPosition = (e) => {
        const rect = canvas.getBoundingClientRect();

        let clientX, clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        return {
            x: (clientX - rect.left) * (canvas.width / rect.width),
            y: (clientY - rect.top) * (canvas.height / rect.height),
        };
    };

    const hidePlaceholder = () => {
        placeholder.style.display = "none";
    };

    const showPlaceholder = () => {
        const blankCanvas = document.createElement("canvas");
        blankCanvas.width = canvas.width;
        blankCanvas.height = canvas.height;
        const blankDataUrl = blankCanvas.toDataURL();
        const currentDataUrl = canvas.toDataURL();

        placeholder.style.display =
            currentDataUrl === blankDataUrl ? "block" : "none";
    };

    const startDraw = (e) => {
        e.preventDefault();
        drawing = true;
        hidePlaceholder();
        const pos = getPosition(e);
        ctx.beginPath();
        ctx.moveTo(pos.x / ratio, pos.y / ratio);
    };

    let lastDrawTime = 0;
    const drawThrottleDelay = 16; // ~60fps

    const draw = (e) => {
        if (!drawing) return;
        e.preventDefault();

        const now = Date.now();
        if (now - lastDrawTime < drawThrottleDelay) return; // skip frame jika terlalu cepat

        lastDrawTime = now;

        const pos = getPosition(e);
        ctx.lineTo(pos.x / ratio, pos.y / ratio);
        ctx.stroke();
    };

    const endDraw = (e) => {
        if (!drawing) return;
        drawing = false;
        document.getElementById("signature-data").value =
            canvas.toDataURL("image/png");
        showPlaceholder();
        validateForm();
    };

    // Event listeners mouse
    canvas.addEventListener("mousedown", startDraw);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", endDraw);
    canvas.addEventListener("mouseleave", endDraw);

    // Event listeners touch
    canvas.addEventListener("touchstart", startDraw, { passive: false });
    canvas.addEventListener("touchmove", draw, { passive: false });
    canvas.addEventListener("touchend", endDraw);

    // Clear button
    const clearBtn = document.getElementById("clear-signature");
    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById("signature-data").value = "";
            showPlaceholder();
            validateForm();
        });
    }

    // Tampilkan placeholder awal
    showPlaceholder();
});

function isCanvasBlank(canvas) {
    const blank = document.createElement("canvas");
    blank.width = canvas.width;
    blank.height = canvas.height;
    return canvas.toDataURL() === blank.toDataURL();
}

function validateForm(showError = false) {
    $(".error-message").remove(); // hapus error lama
    $(".field-error").removeClass("field-error"); // hapus highlight lama

    const lat = $("#latitude").val().trim();
    const lng = $("#longitude").val().trim();

    const detailIds = [
        ...new Set(
            $("[name^='status']")
                .map(function () {
                    return this.name.match(/\d+/)[0];
                })
                .get()
        ),
    ];

    detailIds.forEach(function (detailId) {
        const radios = $(`[name='status[${detailId}]']`);
        const selectGroup = radios.first().closest(".selectgroup");

        // reset class status dulu biar gak numpuk
        selectGroup.removeClass(
            "selectgroup-success selectgroup-warning selectgroup-danger"
        );

        const selectedStatus = radios.filter(":checked").val();

        if (!selectedStatus) {
            if (showError) {
                selectGroup.after(
                    '<div class="error-message text-danger my-1">Choose one of the statuses!</div>'
                );
                selectGroup.addClass("field-error");
            }
            return;
        }

        // tambahkan class sesuai status
        if (selectedStatus === "a") {
            selectGroup.addClass("selectgroup-success");
        } else if (selectedStatus === "b") {
            selectGroup.addClass("selectgroup-warning");
        } else if (selectedStatus === "c") {
            selectGroup.addClass("selectgroup-danger");
        }

        if (selectedStatus === "b") {
            const notes = $(`[name='notes[${detailId}]']`);
            const notesTrimmed = notes.val().trim();
            const imageInput = $(`[name='images[${detailId}]']`);
            const imageWrapper = imageInput.closest(".image-upload");
            const imageFiles = imageInput[0].files.length;

            if (!notesTrimmed) {
                if (showError) {
                    notes.after(
                        '<div class="error-message text-danger mt-1">Notes required!</div>'
                    );
                    notes.addClass("field-error");
                }
            }
            if (imageFiles === 0) {
                if (showError) {
                    imageWrapper.after(
                        '<div class="error-message text-danger mt-1">Capture an image!</div>'
                    );
                    imageWrapper.addClass("field-error");
                }
            }
        }
    });

    const canvas = document.getElementById("signature-pad");
    const signatureWrapper = $(canvas).closest(".signature-container");
    if (!canvas || isCanvasBlank(canvas)) {
        if (showError) {
            signatureWrapper.after(
                '<div class="error-message text-danger mt-1">Digital Signature required!</div>'
            );
            signatureWrapper.addClass("field-error");
        }
    }
}

// Submit handler
$("#checklistForm").on("submit", function (e) {
    e.preventDefault(); // cegah submit dulu
    $(".error-message").remove();
    $(".field-error").removeClass("field-error");

    const lat = $("#latitude").val()?.trim();
    const lng = $("#longitude").val()?.trim();

    const submitForm = () => {
        validateForm(true);
        if ($(".error-message").length > 0) {
            $("html, body").animate(
                { scrollTop: $(".error-message").first().offset().top - 100 },
                500
            );
        } else {
            this.submit();
        }
    };

    if (!lat || !lng) {
        getLocationAndUpdateFields(submitForm, () => {
            $.notify(
                {
                    icon: "icon-bell",
                    title: "Failed",
                    message: "Allow location access, then try again!",
                },
                {
                    type: "danger",
                    placement: { from: "top", align: "right" },
                    delay: 2000,
                }
            );
        });
    } else {
        submitForm();
    }
});

// Change handler
$(function () {
    $.notify(
        {
            icon: "icon-bell",
            title: "Notice",
            message: "Make sure location access is allowed!",
        },
        {
            type: "info",
            placement: {
                from: "top",
                align: "right",
            },
            delay: 2000,
        }
    );

    $(document).on(
        "change input",
        "[name^='status'], [name^='notes'], [name^='images']",
        function () {
            $(".error-message").remove();
            $(".field-error").removeClass("field-error");
            validateForm(false);
        }
    );
});
