<!DOCTYPE html>
<html>
<head>
    <title>Send Test Email</title>
</head>
<body>
    <h1>Send Test Email</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('send.test.email') }}">
        @csrf
        <div>
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button type="submit">Send Test Email</button>
    </form>
</body>
</html>
