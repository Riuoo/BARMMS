<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
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
            background: linear-gradient(to right, #28a745, #a2d46e);
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
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #19692c;
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
            <h1>Account Approved</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>Your account request has been approved!</p>
            <p>You can now <a href="{{ url('/register/' . $token) }}" class="button">Sign Up</a> to the system.</p>
        </div>
        <div class="footer">
            <p>Contact us at: <a href="mailto:support@example.com">support@example.com</a></p>
            <p>&copy; {{ date('Y') }} BARMMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
