@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h4 class="mb-3">Scan QR Shipment</h4>

            <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
            <p id="result" class="mt-3 text-muted">Point the camera at the QR Code...</p>

            {{-- Suara bip --}}
            <audio id="beep-sound">
                <source src="{{ asset('dist/audio/beep_short.ogg') }}" type="audio/ogg">
            </audio>
        </div>
    </div>
</div>

<script src="{{ asset('dist/js/plugin/html5-qrcode/html5-rqcode.min.js') }}" type="text/javascript"></script>
<script>
document.addEventListener("DOMContentLoaded", async function() {
    const html5QrCode = new Html5Qrcode("reader");
    const beepSound = document.getElementById("beep-sound");
    let currentCameraId = null;

    // Fungsi untuk memulai kamera ulang
    async function startCamera() {
        try {
            const devices = await Html5Qrcode.getCameras();

            if (devices && devices.length) {
                currentCameraId = devices[0].id;

                await html5QrCode.start(
                    currentCameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    (decodedText) => {
                        // bunyikan beep
                        beepSound.currentTime = 0;
                        beepSound.play().catch(() => {});

                        // stop scanner dan proses data
                        html5QrCode.stop().then(() => {
                            document.getElementById("result").innerText = "Memproses data...";

                            fetch("{{ route('shipments.scan.process') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({ no_shipment: decodedText })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = data.redirect;
                                } else {
                                    // tampilkan alert dan restart kamera
                                    swal("Warning!", data.message, "warning", {
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        document.getElementById("result").innerText =
                                            "Point the camera at the QR Code...";
                                        startCamera(); // inisialisasi ulang kamera
                                    });
                                }
                            })
                            .catch(err => {
                                console.error("Fetch error:", err);
                                swal("Error!", "Gagal mengirim data ke server.", "error");
                                startCamera();
                            });
                        });
                    },
                    (errorMessage) => {
                        console.log("Scanning...", errorMessage);
                    }
                );
            } else {
                document.getElementById("result").innerText = "No camera detected.";
            }
        } catch (error) {
            document.getElementById("result").innerText = "Error initializing camera.";
            console.error(error);
        }
    }

    // Jalankan pertama kali
    startCamera();
});

</script>
@endsection
