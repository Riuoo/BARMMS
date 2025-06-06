<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forgot Password - Barangay Information System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Forgot Password</h2>
        <p class="text-gray-600 mb-6 text-center">Enter your email address to receive a password reset link.</p>
        
        @if (session('status'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="/forgot-password" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Enter your email" />
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-envelope mr-2"></i>
                Send Reset Link
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="/" class="text-blue-600 hover:text-blue-800 font-medium">Back to Login</a>
        </div>
    </div>
</body>
</html>
