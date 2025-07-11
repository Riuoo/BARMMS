<!DOCTYPE html>
<html>
<head>
    <title>CASE RESOLUTION</title>
    <style>
        body { font-family: 'Times New Roman', serif; margin: 20px; }
        .header { text-align: center; }
        .content { line-height: 1.8; text-align: justify; margin: 30px 0; }
        .footer { margin-top: 50px; }
        .signature { margin-top: 80px; text-align: right; }
        .signature-line { border-top: 1px solid #000; width: 250px; margin-left: auto; }
    </style>
</head>
<body>
    <div class="header">
        <h2>OFFICE OF THE BARANGAY CAPTAIN</h2>
        <h3>BARANGAY {{ Str::upper(config('app.barangay_name')) }}</h3>
        <h1>CASE RESOLUTION</h1>
    </div>

    <div class="content">
        <p>This is to certify that the case filed by <strong>{{ $blotter->user->name }}</strong>
        regarding {{ strtolower($blotter->type) }} has been duly resolved on this date
        {{ now()->format('F j, Y') }}.</p>
        
        <p>The matter was addressed through: {!! nl2br(e($blotter->resolution ?? 'Peaceful mediation and agreement between parties')) !!}</p>
        
        <p>This resolution serves as an official record that this case is now considered closed and
        settled in accordance with barangay procedures.</p>
    </div>

    <div class="footer">
        <div class="signature">
            <div class="signature-line"></div>
            <p>{{ $adminUser->name ?? 'Barangay Captain' }}</p>
            <p>Barangay Captain</p>
        </div>
    </div>
</body>
</html>