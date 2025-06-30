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
    <!-- Navigation -->
    <nav class="bg-green-600 text-white fixed w-full top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold">Lower Malinao System</h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('landing') }}" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                        <a href="#bulletin" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Bulletin Board</a>
                        <a href="#contact" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-green-600">
                <a href="{{ route('landing') }}" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="#bulletin" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Bulletin Board</a>
                <a href="#contact" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Contact</a>
            </div>
        </div>
    </nav>
    
    <main>
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-800 transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                        placeholder="Enter your email" />
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i class="fas fa-envelope mr-2"></i>
                    Send Reset Link
                </button>
            </form>
        </div>
    </main>
</body>
</html>