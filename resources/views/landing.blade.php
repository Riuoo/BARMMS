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
    @notifyCss
    <style>
        .hero-bg {
            background-image: linear-gradient(
                    rgba(0, 0, 0, 0.7),
                    rgba(0, 0, 0, 0.7)
                ),
                url("/images/lower-malinao-brgy-bg-f.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        .notify {
            z-index: 1001 !important;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('notify::components.notify')

    <!-- Enhanced Navigation -->
    <nav class="bg-white/90 backdrop-blur-md text-gray-900 fixed w-full top-0 z-50 shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-10 w-auto" />
                    <h1 class="text-xl font-bold gradient-text">
                        Lower Malinao System
                    </h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#home" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                        <a href="#bulletin" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Bulletin Board</a>
                        <!-- <a href="{{ route('public.accomplishments') }}" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Accomplishments</a> -->
                        <a href="#contact" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
                        <!-- <a href="{{ route('admin.contact') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">Request Account</a> -->
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white/95 backdrop-blur-md border-t border-gray-200">
                <a href="#home" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="#bulletin" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Bulletin Board</a>
                <!-- <a href="{{ route('public.accomplishments') }}" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Accomplishments</a> -->
                <a href="#contact" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Contact</a>
                <!-- <a href="{{ route('admin.contact') }}" class="block bg-green-600 text-white px-3 py-2 rounded-md text-base font-medium">Request Account</a> -->
            </div>
        </div>
    </nav>

    <!-- Enhanced Hero Section -->
    <section id="home" class="hero-bg min-h-screen flex items-center relative">
        <div class="absolute inset-0 bg-gradient-to-r from-green-900/50 to-blue-900/50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Side - Welcome Content -->
                <div class="text-white">
                    <div class="mb-6">
                        <span class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium">Welcome to</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                        Lower Malinao
                    </h1>
                    <p class="text-xl md:text-2xl lg:text-3xl mb-8 text-gray-200 leading-relaxed">
                        Your gateway to community information and services
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#bulletin" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-xl text-lg transition duration-300 inline-flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-bullhorn mr-2"></i>
                            View Bulletin Board
                        </a>
                        <a href="{{ route('admin.contact') }}" class="bg-transparent border-2 border-white text-white font-bold py-4 px-8 rounded-xl text-lg transition duration-300 inline-flex items-center justify-center hover:bg-white hover:text-gray-900">
                            <i class="fas fa-user-plus mr-2"></i>
                            Request Account
                        </a>
                    </div>
                </div>

                <!-- Right Side - Enhanced Login Form -->
                <div class="glass-effect rounded-2xl shadow-2xl p-8 max-w-md mx-auto w-full floating-animation">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-16 w-auto" />
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                                Barangay Portal
                            </h2>
                            <p class="text-gray-600 text-sm">Access your account</p>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6" novalidate>
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-envelope mr-2 text-green-600"></i>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror" 
                                placeholder="Enter your email" required />
                            @error('email')
                                <p class="text-red-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-lock mr-2 text-green-600"></i>
                                Password
                            </label>
                            <input type="password" id="password" name="password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror" 
                                placeholder="Enter your password" required />
                            @error('password')
                                <p class="text-red-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember Me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-4 rounded-xl transition duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('admin.contact') }}" class="text-green-600 hover:text-green-800 font-medium">
                                Request one here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Bulletin Board Section -->
    <section id="bulletin" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Community Accomplishments</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Discover the transformative projects and initiatives that have improved our community's quality of life</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $featuredProjects = \App\Models\AccomplishedProject::where('is_featured', true)
                        ->orderBy('completion_date', 'desc')
                        ->take(6)
                        ->get();
                @endphp
                
                @forelse($featuredProjects as $project)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 overflow-hidden border border-gray-100">
                    @if($project->image_url)
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ $project->image_url }}" alt="{{ $project->title }} image" class="object-cover h-full w-full">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <i class="fas fa-project-diagram text-4xl text-gray-400"></i>
                        </div>
                    @endif
                    
                    <!-- Project Content -->
                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $project->category_color }}">
                                {{ $project->category }}
                            </span>
                            <span class="text-yellow-500">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                        
                        <!-- Project Title -->
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $project->title }}</h3>
                        
                        <!-- Project Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($project->description, 120) }}</p>
                        
                        <!-- Project Details -->
                        <div class="space-y-2 mb-4">
                            @if($project->location)
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                                <span>{{ $project->location }}</span>
                            </div>
                            @endif
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                <span>{{ $project->completion_date->format('M Y') }}</span>
                            </div>
                            @if($project->budget)
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-money-bill mr-2 text-green-600"></i>
                                <span>{{ $project->formatted_budget }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Impact Preview -->
                        @if($project->impact)
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-600">
                                <strong>Impact:</strong> {{ Str::limit($project->impact, 80) }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-project-diagram text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Featured Projects Yet</h3>
                    <p class="text-gray-600">Our community accomplishments will be displayed here soon.</p>
                </div>
                @endforelse
            </div>
            
            @if($featuredProjects->count() > 0)
            <div class="text-center mt-12">
                <a href="{{ route('public.accomplishments') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-300">
                    <i class="fas fa-eye mr-2"></i>
                    View All Projects
                </a>
            </div>
            @endif
        </div>
    </section>



    <!-- Enhanced Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Get in Touch</h2>
                <p class="text-xl text-gray-600">We're here to help and answer any questions you might have</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div class="bg-white rounded-2xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-map-marker-alt text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Address</p>
                                    <p class="text-gray-600">Lower Malinao, Barangay Hall</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Phone</p>
                                    <p class="text-gray-600">+63 XXX XXX XXXX</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-envelope text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Email</p>
                                    <p class="text-gray-600">info@lowermalinao.gov.ph</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Office Hours</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="font-medium text-gray-900">Monday - Friday</span>
                            <span class="text-gray-600">8:00 AM - 5:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="font-medium text-gray-900">Saturday</span>
                            <span class="text-gray-600">8:00 AM - 12:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="font-medium text-gray-900">Sunday</span>
                            <span class="text-gray-600">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-12 w-auto mr-3" />
                        <h3 class="text-xl font-bold">Lower Malinao System</h3>
                    </div>
                    <p class="text-gray-400 mb-4">Empowering our community through digital innovation and better service delivery.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="#bulletin" class="text-gray-400 hover:text-white transition duration-300">Bulletin Board</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
                        <li><a href="{{ route('admin.contact') }}" class="text-gray-400 hover:text-white transition duration-300">Request Account</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Services</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Document Requests</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Blotter Reports</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Health Monitoring</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Community Projects</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 Lower Malinao Barangay System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @notifyJs
</body>
</html>
