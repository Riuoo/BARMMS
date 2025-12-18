<!DOCTYPE html>
<html>
<head>
    <title>Account Request Rejected</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(to right, #dc3545, #e57373);
            color: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
        }
        .rejection-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .rejection-box strong {
            color: #856404;
        }
        .duplicate-note {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .duplicate-note strong {
            color: #0d47a1;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Request Rejected</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>We regret to inform you that your account request has been rejected.</p>
            
            <div class="rejection-box">
                <strong>Rejection Reason:</strong>
                <p>{{ $rejectionReason }}</p>
            </div>

            @if($isDuplicate)
            <div class="duplicate-note">
                <strong>Note:</strong>
                <p>It is recommended to visit the barangay office if you have forgotten your previous registration.</p>
            </div>
            @endif

            <p>If you believe this is an error or have any questions, please contact the barangay office for assistance.</p>
        </div>
        <div class="footer">
            <p>Contact us at: <a href="mailto:onelowermalinao@gmail.com">onelowermalinao@gmail.com</a></p>
            <p>&copy; {{ date('Y') }} BARMMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
