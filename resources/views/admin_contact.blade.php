<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Administrator - Barangay Information System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gray-900 text-white fixed w-full top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold">Barangay Information System</h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('landing') }}" class="hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                        <a href="#bulletin" class="hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Bulletin Board</a>
                        <a href="#contact" class="hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
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
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-800">
                <a href="{{ route('landing') }}" class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="#bulletin" class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium">Bulletin Board</a>
                <a href="#contact" class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20 flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Contact Administrator</h2>
            <p class="text-gray-600 mb-6 text-center">Please fill out the form below to send a message to the administrator.</p>
            <form action="{{ route('admin.contact.submit') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="Enter your full name" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="Enter your email address" />
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" id="subject" name="subject" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="Subject of your message" />
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="message" rows="5" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="Write your message here"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Send Message
                </button>
            </form>
        </div>
    </main>

    <!-- JavaScript for mobile menu -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
