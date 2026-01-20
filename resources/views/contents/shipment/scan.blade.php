@extends('layouts.app')

@section('content')
<div class="page-inner">
    @include('layouts.breadcrumb')

    <div class="row">
        <div class="col-12">
            <div class="card card-stats card-round">
                <div class="card-header">
                    <h5 class="card-title">
                        <div class="row">
                            <div class="col-7">
                                <a href="javascript:history.back()">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <span class="ms-4">{{ $title }}</span>
                            </div>
                        </div>
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="mb-3"><i class="fas fa-qrcode me-2"></i> Scan QR Shipment</h4>

                    <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                    
                    {{-- Input manual --}}
                    <div class="my-3">
                        <h6 class="text-muted mb-3">or enter the shipment number manually</h6>
                        <div class="input-group" style="max-width: 400px; margin: 0 auto;">
                            <input type="text" id="manualInput" class="form-control text-center text-uppercase" autofocus>
                            <button id="manualSubmit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div id="manualError" class="mt-2 text-danger fw-bold"></div>
                    <p id="result" class="mt-3 text-muted"></p>

                    {{-- Suara bip --}}
                    <audio id="beep-sound">
                        <source src="{{ asset('dist/audio/beep_short.ogg') }}" type="audio/ogg">
                    </audio>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('dist/js/plugin/html5-qrcode/html5-rqcode.min.js') }}" type="text/javascript"></script>
<script>
document.addEventListener("DOMContentLoaded", async function() {
    const html5QrCode = new Html5Qrcode("reader");
    const beepSound = document.getElementById("beep-sound");
    const resultEl = document.getElementById("result");
    const inputEl = document.getElementById("manualInput");
    const submitBtn = document.getElementById("manualSubmit");
    const manualError = document.getElementById("manualError");
    let scanCooldown = false;

    function setScannerColor(color = "#00FF00") {
        const qrRegion = document.querySelector("#reader__scan_region");
        if (qrRegion) {
            qrRegion.style.border = `4px solid ${color}`;
            qrRegion.style.transition = "border-color 0.3s ease";
            qrRegion.style.borderRadius = "12px";
        }
    }

    // proses pengiriman shipment
    function processShipment(noShipment, isManual = false) {
        if (!noShipment) return;

        scanCooldown = true;
        manualError.innerHTML = "";
        resultEl.innerText = isManual ? "" : "Processing data...";

        if (!isManual) {
            beepSound.currentTime = 0;
            beepSound.play().catch(() => {});
            // Tambahkan getaran selama 200ms
            if (navigator.vibrate) {
                navigator.vibrate(200);
            }
        }

        setScannerColor("#00FF00");

        fetch("{{ route('shipments.scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ no_shipment: noShipment.trim().toUpperCase() })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                html5QrCode.stop().then(() => {
                    inputEl.value = "";
                    window.location.href = data.redirect;
                });
            } else {
                const message = data.message || 'Shipment not available.';

                // pesan error disesuaikan
                if (isManual) {
                    swal("Warning!", message, "warning", {
                        timer: 1500,
                        button:false
                    });
                    
                    inputEl.value = "";
                    inputEl.focus();
                    // manualError.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>Shipment number not found.`;
                } else {
                    setScannerColor("#FF0000");
                    swal("Warning!", message, "warning", {
                        timer: 1500,
                        button:false
                    });

                    inputEl.focus();
                    // resultEl.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Invalid QR Code!</span>`;
                }

                setTimeout(() => {
                    if (!isManual) {
                        resultEl.innerText = "";
                        setScannerColor("#00FF00");
                        inputEl.focus();
                    }
                    scanCooldown = false;
                }, 2000);
            }
        })
        .catch(() => {
            if (isManual) {
                manualError.innerHTML = `<i class="fas fa-times-circle me-2"></i>Failed to send data to the server.`;
                inputEl.focus();
            } else {
                setScannerColor("#FF0000");
                resultEl.innerHTML = `<span class="text-danger">Failed to send data to the server.</span>`;
                inputEl.focus();
            }
            setTimeout(() => {
                if (!isManual) {
                    resultEl.innerText = "";
                    setScannerColor("#00FF00");
                    inputEl.focus();
                }
                scanCooldown = false;
            }, 2000);
        });
    }

    // event tombol manual
    submitBtn.addEventListener("click", () => {
        if (scanCooldown) return;
        const noShipment = inputEl.value;
        processShipment(noShipment, true);
    });

    inputEl.addEventListener("keyup", (e) => {
        if (e.key === "Enter" && !scanCooldown) {
            processShipment(inputEl.value, true);
        }
    });

    // mulai kamera
    async function startCamera() {
        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                const backCamera = devices.find(d => d.label.toLowerCase().includes("back") || d.label.toLowerCase().includes("rear"));
                const cameraId = backCamera ? backCamera.id : devices[0].id;

                await html5QrCode.start(
                    cameraId,
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        if (scanCooldown) return;
                        processShipment(decodedText, false);
                    },
                    () => {}
                );
                setTimeout(() => setScannerColor("#00FF00"), 500);
            }
        } catch (error) {
            console.error(error);
        }
    }

    startCamera();
});
</script>
@endsection
