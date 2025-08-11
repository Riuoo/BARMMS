<!DOCTYPE html>
<html>
<head>
    <title>OFFICIAL SUMMON NOTICE</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .content { line-height: 1.6; }
        .footer { margin-top: 50px; text-align: right; font-style: italic; }
        .signature { margin-top: 100px; border-top: 1px solid #000; width: 250px; padding-top: 10px; }
        .important { font-weight: bold; color: #d33; }
    </style>
</head>
<body>
    <div class="header">
        <h1>OFFICIAL SUMMON NOTICE</h1>
        <h3>BARANGAY {{ Str::upper(config('app.barangay_name')) }}</h3>
    </div>

    <div class="content">
        <p>To: <strong>{{ $blotter->recipient_name }}</strong></p>
        <p>Address: {{ $blotter->resident->address ?? 'Not specified' }}</p>
        <p>Contact: {{ $blotter->resident->contact_number ?? 'Not specified' }}</p>
        
        <p class="important">You are hereby summoned to appear before the Barangay Office on:</p>
        <p><strong>{{ \Carbon\Carbon::parse($blotter->summon_date)->format('l, F j, Y \a\t g:i A') }}</strong></p>
        
        <p>Regarding: <strong>{{ $blotter->type }}</strong></p>
        <p>Case Details: {!! nl2br(e($blotter->description)) !!}</p>
        
        <p class="important">Failure to appear may result in further legal action.</p>
    </div>

    <div class="footer">
        <p>Issued on: {{ now()->format('F j, Y') }}</p>
        <div class="signature">
            <p>_________________________</p>
            <p>{{ $adminUser->name ?? 'Barangay Official' }}</p>
            <p>Barangay Official</p>
        </div>
    </div>
</body>
</html>