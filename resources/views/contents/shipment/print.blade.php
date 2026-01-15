<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Shipment - {{ strtoupper($shipment->no_shipment) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #fff;
            color: #000;
        }

        .print-wrapper {
            border: 5px solid #000;
            border-radius: 8px;
            padding: 25px 35px;
            margin: 0 auto 20px auto;
            position: relative;
            zoom: 0.7;
        }

        .shipment-container {
            display: flex;
            /* justify-content: space-between; */
            align-items: stretch;
        }

        /* Kiri: QR */
        .qr-section {
            width: 30%;
            text-align: center;
            padding-right: 10px;
        }

        .qr-section img {
            width: 100%;
        }

        .qr-section h5 {
            margin-top: 10px;
            font-size: 25px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Tengah: detail pengirim dan penerima */
        .detail-section {
            width: 62%;
            padding-left: 10px;
            display: flex;
            flex-direction: column;
        }

        .info-box {
            padding: 7px 15px;
        }

        .info-box2 {
            padding-top: 20px;
        }

        .info-box:not(:last-child) {
            margin-bottom: 10px;
        }

        .info-title {
            font-weight: bold;
            font-size: 22px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            font-size: 22px;
            vertical-align: top;
        }

        td:first-child {
            width: 16%;
        }

        td:last-child {
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Kolom kanan: DC Support vertical text */
        .dc-support-side {
            width: 8%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-dark {
            background-color: #000000;
            color: #000000;
        }

        .bg-primary {
            background-color: #177dff;
            color: #ffffff;
        }

        .bg-warning {
            background-color: #ffe134;
            color: #f3545d;
        }

        .dc-support-text {
            writing-mode: vertical-rl;
            transform: rotate(360deg);
            font-size: 30px;
            font-weight: bolder;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Footer warning */
        .shipment-footer {
            text-align: center;
            margin-top: 5px;
        }

        .fragile-warning {
            display: inline-block;
            border: 3px dashed #ff0000;
            color: #ff0000;
            padding: 5px 18px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 30px;
            border-radius: 6px;
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @for ($i = 1; $i <= $copies; $i++)
        <div class="print-wrapper">
            <div class="shipment-container">
                <!-- QR Section -->
                <div class="qr-section">
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                    <h5>{{ strtoupper($shipment->no_shipment) }}</h5>
                </div>

                <!-- Detail Section -->
                <div class="detail-section">
                    <div class="info-box">
                        <div class="info-title">PENGIRIM</div>
                        <table>
                            <tr>
                                <td>Lokasi</td>
                                <td>: {{ strtoupper(optional($shipment->sender_location)->clean_name ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td>PIC</td>
                                <td>: {{ strtoupper($shipment->sender_pic) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="info-box">
                        <div class="info-title">PENERIMA</div>
                        <table>
                            <tr>
                                <td>Lokasi</td>
                                <td>: {{ strtoupper(optional($shipment->receiver_location)->clean_name ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td>PIC</td>
                                <td>: {{ strtoupper($shipment->destination_pic ?? '-') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="info-box">
                        <div class="info-title">INFORMASI LAINNYA</div>
                        <table>
                            <tr>
                                <td style="width: 16%">Kategori</td>
                                <td style="width: 53%;font-weight:bold;">:
                                    {{ strtoupper($shipment->category->name ?? 'Unknown') }}
                                </td>
                                <td style="width: 15%">Packing</td>
                                <td style="width: 16%">: {{ $shipment->packing ?? '0' }} Koli</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Kolom Kanan: Tulisan DC Support Vertikal -->
                @php
                    $dc = strtoupper($shipment->dc_support);
                    $bgClass = [
                        'D53' => 'bg-primary',
                        'GBG' => 'bg-warning',
                    ][$dc] ?? 'bg-dark';
                @endphp

                <div class="dc-support-side {{ $shipment->shipment_by == '2' ? $bgClass : 'bg-dark' }}">
                    <div class="dc-support-text">
                        {{ $shipment->shipment_by == '2' ? $dc : '' }}
                    </div>
                </div>
            </div>

            {{-- Footer hanya jika fragile --}}
            @if($shipment->handling_level == '2')
            <div class="shipment-footer">
                <div class="fragile-warning">⚠️ FRAGILE - HANDLE WITH CARE ⚠️</div>
            </div>
            @endif
        </div>

        @if($i < $copies)
            <div class="page-break"></div>
        @endif
    @endfor

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // beri sedikit delay agar halaman benar-benar ter-render
            setTimeout(() => {
                window.focus();
                window.print();

                // opsional: tutup otomatis setelah print
                window.close();
            }, 700);
        });
    </script>
</body>
</html>
