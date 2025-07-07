<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lower Malinao System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <style>
        .hero-bg {
            background-image: linear-gradient(
                    rgba(0, 0, 0, 0.6),
                    rgba(0, 0, 0, 0.6)
                ),
                url("/images/lower-malinao-brgy-bg-f.png");
            background-size: cover; /* Ensures the image covers the entire section */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents the image from repeating */
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav
        class="bg-green-600 text-white fixed w-full top-0 z-50 shadow-lg"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold">
                        Lower Malinao System
                    </h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a
                            href="#home"
                            class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300"
                            >Home</a
                        >
                        <a
                            href="#bulletin"
                            class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300"
                            >Bulletin Board</a
                        >
                        <a
                            href="#contact"
                            class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300"
                            >Contact</a
                        >
                    </div>
                </div>
                <div class="md:hidden">
                    <button
                        id="mobile-menu-button"
                        class="text-gray-300 hover:text-white focus:outline-none"
                    >
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-800">
                <a
                    href="#home"
                    class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium"
                    >Home</a
                >
                <a
                    href="#bulletin"
                    class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium"
                    >Bulletin Board</a
                >
                <a
                    href="#contact"
                    class="block hover:bg-gray-700 px-3 py-2 rounded-md text-base font-medium"
                    >Contact</a
                >
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Side - Welcome Content -->
                <div class="text-white">
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6"
                    >
                        Welcome to Lower Malinao
                    </h1>
                    <p class="text-lg md:text-xl lg:text-2xl mb-8">
                        Your gateway to community information and services
                    </p>
                    <a
                        href="#bulletin"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition duration-300 inline-block"
                    >
                        View Bulletin Board
                    </a>
                </div>

                <!-- Right Side - Login Form -->
                <div
                    class="bg-white bg-opacity-95 rounded-lg shadow-2xl p-8 max-w-md mx-auto w-full"
                >
                    <div class="flex items-center justify-center gap-4">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-20 w-auto" />
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                Barangay Portal
                            </h2>
                        </div>
                    </div>
                    <p class="text-gray-600 text-center">Access your account</p>

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6" novalidate>
                        @csrf
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Email Address</label
                            >
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror" placeholder="Enter your email" required
                            />
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Password</label
                            >
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror" placeholder="Enter your password" required
                            />
                            @error('password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Remember Me</label>
                                </div>
                            </div>
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm text-green-600 hover:text-green-800"
                                >Forgot password?</a
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a
                                href="{{ route('admin.contact') }}"
                                class="text-green-600 hover:text-green-800 font-medium"
                                >Contact Administrator</a
                            >

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bulletin Board Section -->
    <section id="bulletin" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-4xl font-bold text-center text-gray-900 mb-12"
            >
                Bulletin Board
            </h2>

            <!-- Upcoming Events -->
            <div class="mb-16">
                <h3 class="text-3xl font-bold text-gray-900 mb-8">
                    Upcoming Events
                </h3>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                >
                    <div
                        class="bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-lg p-6"
                    >
                        <div class="flex items-center mb-4">
                            <i
                                class="fas fa-calendar-alt text-blue-600 text-2xl mr-3"
                            ></i>
                            <span
                                class="text-sm text-blue-600 font-semibold"
                                >December 15, 2024</span
                            >
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-3">
                            Community Christmas Party
                        </h4>
                        <p class="text-gray-700 mb-4">
                            Join us for our annual Christmas celebration with
                            games, food, and prizes for the whole family.
                        </p>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i>Barangay Hall
                        </div>
                    </div>

                    <div
                        class="bg-green-50 border-l-4 border-green-500 rounded-lg shadow-lg p-6"
                    >
                        <div class="flex items-center mb-4">
                            <i
                                class="fas fa-calendar-alt text-green-600 text-2xl mr-3"
                            ></i>
                            <span
                                class="text-sm text-green-600 font-semibold"
                                >December 20, 2024</span
                            >
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-3">
                            Health and Wellness Seminar
                        </h4>
                        <p class="text-gray-700 mb-4">
                            Free health screening and wellness seminar for all
                            residents. Bring your health records.
                        </p>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i>Community
                            Center
                        </div>
                    </div>

                    <div
                        class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-lg p-6"
                    >
                        <div class="flex items-center mb-4">
                            <i
                                class="fas fa-calendar-alt text-yellow-600 text-2xl mr-3"
                            ></i>
                            <span
                                class="text-sm text-yellow-600 font-semibold"
                                >January 5, 2025</span
                            >
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-3">
                            Skills Training Workshop
                        </h4>
                        <p class="text-gray-700 mb-4">
                            Learn new skills in cooking, sewing, and basic
                            computer literacy. Registration required.
                        </p>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2"></i>Training
                            Center
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barangay Achievements -->
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mb-8">
                    Barangay Achievements
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg shadow-lg p-8 text-white"
                    >
                        <div class="flex items-center mb-4">
                            <i
                                class="fas fa-trophy text-4xl mr-4"
                            ></i>
                            <div>
                                <h4 class="text-2xl font-bold">
                                    Outstanding Barangay Award
                                </h4>
                                <p class="text-purple-100">November 2024</p>
                            </div>
                        </div>
                        <p class="text-purple-100">
                            Recognized as the Most Outstanding Barangay in
                            Community Development and Public Service Excellence.
                        </p>
                    </div>

                    <div
                        class="bg-gradient-to-r from-green-500 to-teal-500 rounded-lg shadow-lg p-8 text-white"
                    >
                        <div class="flex items-center mb-4">
                            <i
                                class="fas fa-leaf text-4xl mr-4"
                            ></i>
                            <div>
                                <h4 class="text-2xl font-bold">
                                    Cleanest Barangay Award
                                </h4>
                                <p class="text-green-100">October 2024</p>
                            </div>
                        </div>
                        <p class="text-green-100">
                            Awarded for maintaining the cleanest and most
                            environmentally-friendly community in the district.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Contact Us
                </h2>
                <p class="text-xl text-gray-600 mb-8">
                    Get in touch with us for any inquiries or concerns.
                </p>

                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Office Hours:
                    </h3>
                    <p class="text-gray-700">Monday to Friday: 8:00 AM - 5:00 PM</p>
                </div>

                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Contact Information:
                    </h3>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-phone text-green-600 mr-4"></i>
                        <span class="text-gray-700">(123) 456-7890</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-envelope text-green-600 mr-4"></i>
                        <span class="text-gray-700">info@barangay.gov.ph</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-green-600 mr-4"></i>
                        <span class="text-gray-700">Barangay Hall, Main Street</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">
                        Barangay Information System
                    </h3>
                    <p class="text-gray-300">
                        Serving the community with dedication and excellence.
                    </p>
                </div>
                <div class="md:text-right">
                    <h3 class="text-2xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <a
                                href="#home"
                                class="text-gray-300 hover:text-white transition duration-300"
                                >Home</a
                            >
                        </li>
                        <li>
                            <a
                                href="#bulletin"
                                class="text-gray-300 hover:text-white transition duration-300"
                                >Bulletin Board</a
                            >
                        </li>
                        <li>
                            <a
                                href="#contact"
                                class="text-gray-300 hover:text-white transition duration-300"
                                >Contact</a
                            >
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="border-gray-700 my-8" />
            <div class="text-center">
                <p class="text-gray-300">
                    &copy; 2024 Barangay Information System. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for mobile menu -->
    <script>
        document
            .getElementById("mobile-menu-button")
            .addEventListener("click", function () {
                const mobileMenu = document.getElementById("mobile-menu");
                mobileMenu.classList.toggle("hidden");
            });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener("click", function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute("href"));
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start",
                    });
                }
            });
        });
    </script>
</body>
</html>
