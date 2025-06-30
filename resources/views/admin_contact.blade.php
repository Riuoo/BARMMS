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

    <!-- Main Content -->
    <main class="flex-grow pt-20 flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-800 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h2 class="text-3xl font-bold text-gray-900 text-center flex-1 mr-8">Contact Administrator</h2>
            </div>
            <p class="text-gray-600 mb-6 text-center">Please enter your email address below to request an account. The administrator will contact you with a link to complete your account creation.</p>
           <form action="{{ route('admin.contact') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                        placeholder="Enter your email address" />
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Send Request
                </button>
            </form>

        </div>
    </main>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success); // Display the success message
                    window.location.reload(); // Reload the page
                } else if (data.error) {
                    alert(data.error); // Display the error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('This email has already been submitted. Please check your email for a response.');
            });
        });
    </script>

    <!-- JavaScript for mobile menu -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
