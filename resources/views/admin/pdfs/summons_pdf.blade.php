<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Summon Notice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 6px 0; }
        h2 { font-size: 14px; margin: 14px 0 6px 0; }
        .meta { margin: 8px 0 14px 0; }
        .row { margin: 6px 0; }
        .box { border: 1px solid #444; padding: 10px; border-radius: 4px; }
        .muted { color: #555; }
        .footer { margin-top: 30px; font-size: 11px; color: #555; }
        .sig { margin-top: 40px; }
        .sig .line { border-top: 1px solid #333; width: 240px; margin-top: 40px; }
        .small { font-size: 11px; }
    </style>
    </head>
<body>
    <h1>Summon Notice</h1>
    <div class="meta">
        <div class="row"><strong>Case ID:</strong> {{ $blotter->id }}</div>
        <div class="row"><strong>Status:</strong> {{ ucfirst($blotter->status) }}</div>
        <div class="row"><strong>Summon Date:</strong> {{ optional($blotter->summon_date)->format('F d, Y g:i A') }}</div>
        <div class="row"><strong>Approved At:</strong> {{ optional($blotter->approved_at)->format('F d, Y g:i A') }}</div>
    </div>

    <div class="box">
        <h2>Parties</h2>
        <div class="row"><strong>Complainant:</strong> {{ $blotter->complainant_name ?? 'N/A' }}</div>
        <div class="row"><strong>Respondent:</strong> {{ optional($blotter->respondent)->full_name ?? 'N/A' }}</div>
    </div>

    <h2>Incident Description</h2>
    <div class="box">
        <div class="row">{{ $blotter->description }}</div>
    </div>

    <div class="footer">
        This notice is issued by the Barangay for the purpose of summoning the respondent for mediation/conciliation.
    </div>

    <div class="sig">
        <div class="line"></div>
        <div class="small">
            Authorized Officer: {{ optional($adminUser)->full_name ?? 'Barangay Office' }}
        </div>
    </div>
</body>
</html>


