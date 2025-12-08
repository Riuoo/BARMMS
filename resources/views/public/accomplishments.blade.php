<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Community Accomplishments - Lower Malinao System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
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

        /* Pagination specific styles to prevent visual artifacts */
        .pagination-nav {
            position: relative;
        }
        
        .pagination-nav * {
            position: relative;
        }
        
        .pagination-nav .inline-flex {
            position: relative;
        }
        
        /* Ensure no unwanted dots or artifacts */
        .pagination-nav span,
        .pagination-nav a {
            position: relative;
            background: transparent;
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

    <!-- Community Bulletin Board (Projects + Activities) -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Community Bulletin Board</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Browse completed projects and barangay activities across our community</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-center mb-8 gap-3">
                <button id="filter-projects" type="button" class="px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-project-diagram mr-2"></i>
                    All Projects
                </button>
                <button id="filter-activities" type="button" class="px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-bullhorn mr-2"></i>
                    All Activities
                </button>
            </div>
            
            @php $gridItems = isset($bulletin) ? $bulletin : collect(); @endphp

            <div id="bulletinGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($gridItems as $item)
                <div class="bulletin-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 overflow-hidden border border-gray-100" data-type="{{ $item->type ?? 'project' }}">
                    @if($item->image_url)
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }} image" class="object-cover h-full w-full">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <i class="fas {{ ($item->type ?? 'project') === 'activity' ? 'fa-heartbeat' : 'fa-project-diagram' }} text-4xl text-gray-400"></i>
                        </div>
                    @endif

                    <!-- Card Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $item->category ?? (($item->type ?? 'project') === 'activity' ? 'Health' : 'Project') }}
                            </span>
                            <span class="text-xs {{ ($item->type ?? 'project') === 'activity' ? 'text-green-600' : 'text-blue-600' }}">
                                <i class="fas {{ ($item->type ?? 'project') === 'activity' ? 'fa-heartbeat' : 'fa-project-diagram' }} mr-1"></i>
                                {{ ucfirst($item->type ?? 'project') }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($item->description, 120) }}</p>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span><i class="fas fa-calendar-alt mr-1"></i>{{ optional($item->date)->format('M d, Y') }}</span>
                            @if(!empty($item->link))
                            <a href="{{ $item->link }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-bullhorn text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Items Available</h3>
                    <p class="text-gray-600">Community updates will be displayed here soon.</p>
                </div>
                @endforelse
            </div>

            <!-- No results after filter -->
            <p id="noFilteredItems" class="hidden text-center text-gray-500 mt-5">No items to show for this filter.</p>

            <!-- Custom Pagination for Bulletin -->
            @php
                // Decide which dataset to use for pagination
                $paginator = null;

                if (isset($bulletin) && $bulletin->count()) {
                    $paginator = $bulletin;
                } elseif (isset($projects) && $projects->count()) {
                    $paginator = $projects;
                }
            @endphp

            @if($paginator && $paginator->hasPages())
            <div class="mt-12">
                <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 pagination-nav">
                    <div class="-mt-px flex w-0 flex-1">
                        @if($paginator->onFirstPage())
                            <span class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </span>
                        @else
                            <a href="{{ $paginator->appends(request()->except('page'))->previousPageUrl() }}" 
                            class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-3 text-gray-400"></i>
                                Previous
                            </a>
                        @endif
                    </div>
                    
                    <div class="hidden md:-mt-px md:flex">
                        @php
                            $currentPage = $paginator->currentPage();
                            $lastPage = $paginator->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($startPage > 1)
                            <a href="{{ $paginator->appends(request()->except('page'))->url(1) }}" 
                            class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                1
                            </a>
                            @if($startPage > 2)
                                <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                    ...
                                </span>
                            @endif
                        @endif
                        
                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <span class="inline-flex items-center border-t-2 border-green-500 px-4 pt-4 text-sm font-medium text-green-600" aria-current="page" style="position: relative; z-index: 1;">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $paginator->appends(request()->except('page'))->url($page) }}" 
                                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor
                        
                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $paginator->appends(request()->except('page'))->url($lastPage) }}" 
                            class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>
                    
                    <div class="-mt-px flex w-0 flex-1 justify-end">
                        @if($paginator->hasMorePages())
                            <a href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}" 
                            class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Next
                                <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                            </a>
                        @else
                            <span class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500">
                                Next
                                <i class="fas fa-arrow-right ml-3 text-gray-400"></i>
                            </span>
                        @endif
                    </div>
                </nav>
                
                <!-- Mobile Pagination -->
                <div class="mt-4 flex justify-between sm:hidden">
                    @if($paginator->onFirstPage())
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                            Previous
                        </span>
                    @else
                        <a href="{{ $paginator->appends(request()->except('page'))->previousPageUrl() }}" 
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                        Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                    </span>
                    
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}" 
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500">
                            Next
                        </span>
                    @endif
                </div>
                
                <!-- Results Info -->
                <div class="mt-4 text-center text-sm text-gray-500">
                    @if($paginator->total() > 0)
                        @if($paginator->firstItem() && $paginator->lastItem())
                            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
                        @else
                            Showing {{ $paginator->total() }} results
                        @endif
                    @else
                        No results found
                    @endif
                </div>
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

        // Bulletin filter buttons
        const btnProjects = document.getElementById('filter-projects');
        const btnActivities = document.getElementById('filter-activities');
        const grid = document.getElementById('bulletinGrid');
        const items = () => Array.from(grid.querySelectorAll('.bulletin-item'));
        const noFilteredEl = document.getElementById('noFilteredItems');

        function setActive(button) {
            [btnProjects, btnActivities].forEach(b => b.classList.remove('ring-2','ring-green-500','bg-green-50','text-green-700','border-green-300'));
            button.classList.add('ring-2','ring-green-500','bg-green-50','text-green-700','border-green-300');
        }

        function applyFilter(type) {
            let anyVisible = false;
            items().forEach(el => {
                const matches = type === 'all' ? true : (el.dataset.type === type);
                el.classList.toggle('hidden', !matches);
                if (matches) anyVisible = true;
            });
            noFilteredEl.classList.toggle('hidden', anyVisible);
        }

        if (btnProjects && btnActivities && grid) {
            btnProjects.addEventListener('click', () => { setActive(btnProjects); applyFilter('project'); });
            btnActivities.addEventListener('click', () => { setActive(btnActivities); applyFilter('activity'); });
        }
    </script>
</body>
</html> 