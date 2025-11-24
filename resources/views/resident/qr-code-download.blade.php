<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download QR Code - {{ $resident->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .qr-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .qr-code-wrapper {
            margin: 30px 0;
            padding: 20px;
            background: white;
            border: 2px solid #ddd;
            display: inline-block;
        }
        .download-btn {
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .download-btn:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="qr-container" id="qrContainer">
        <h1>{{ $resident->name }}</h1>
        <p>{{ $resident->email }}</p>
        <div class="qr-code-wrapper">
            <div id="qrcode"></div>
        </div>
        <p style="color: #666; font-size: 14px;">Barangay QR Code Identity</p>
        <button class="download-btn" onclick="downloadQR()">
            <i class="fas fa-download"></i> Download as Image
        </button>
    </div>

    <script>
        const qrCodeData = @json($qrCodeData);
        
        new QRCode(document.getElementById("qrcode"), {
            text: qrCodeData,
            width: 400,
            height: 400,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        function downloadQR() {
            html2canvas(document.getElementById('qrContainer')).then(function(canvas) {
                const link = document.createElement('a');
                link.download = 'qr-code-{{ str_replace(" ", "-", strtolower($resident->name)) }}.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        }

        // Auto-download after 1 second
        setTimeout(downloadQR, 1000);
    </script>
</body>
</html>

