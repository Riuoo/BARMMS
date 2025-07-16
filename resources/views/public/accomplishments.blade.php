<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Community Accomplishments - Lower Malinao System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .hero-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url("/images/lower-malinao-brgy-bg-f.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
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
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md text-gray-900 fixed w-full top-0 z-50 shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-10 w-auto" />
                    <h1 class="text-xl font-bold gradient-text">Lower Malinao System</h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                        <a href="/#contact" class="hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
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
                <a href="/" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="/accomplishments" class="block bg-green-100 text-green-700 px-3 py-2 rounded-md text-base font-medium">Accomplishments</a>
                <a href="/#contact" class="block hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">Contact</a>
                <a href="{{ route('admin.contact') }}" class="block bg-green-600 text-white px-3 py-2 rounded-md text-base font-medium">Request Account</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg pt-20 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Community Accomplishments</h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto">
                    Discover the transformative projects and initiatives that have improved our community's quality of life
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-home mr-2"></i>
                        Back to Home
                    </a>
                    <a href="{{ route('admin.contact') }}" class="bg-transparent border-2 border-white text-white font-bold py-3 px-6 rounded-xl transition duration-300 inline-flex items-center justify-center hover:bg-white hover:text-gray-900">
                        <i class="fas fa-user-plus mr-2"></i>
                        Request Account
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">All Community Projects</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Browse through all our completed projects and see how we've transformed our community</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($projects as $project)
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
                            @if($project->is_featured)
                                <span class="text-yellow-500">
                                    <i class="fas fa-star"></i>
                                </span>
                            @endif
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
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Projects Available</h3>
                    <p class="text-gray-600">Our community projects will be displayed here soon.</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($projects->hasPages())
            <div class="mt-12">
                {{ $projects->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
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
                        <li><a href="/" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                        <li><a href="/accomplishments" class="text-gray-400 hover:text-white transition duration-300">Accomplishments</a></li>
                        <li><a href="/#contact" class="text-gray-400 hover:text-white transition duration-300">Contact</a></li>
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
</body>
</html> 