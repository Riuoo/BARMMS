<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans">

    <!-- Header -->
    <header class="bg-white text-gray-900 flex items-center justify-between p-4 shadow-md">
        <div class="flex items-center space-x-4">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-900 focus:outline-none mr-2" aria-label="Toggle sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex items-center space-x-2 text-green-600 font-bold text-2xl">
                <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-12 w-auto" />
                <span>Lower Malinao</span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative cursor-pointer" title="Notifications" x-data="{ open: false }" @click="open = !open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" role="img" aria-label="Notifications Icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                {{-- Notification count badge --}}
                <span id="notification-count-badge" class="absolute top-0 right-0 bg-red-600 text-white rounded-full px-1 text-xs" style="display: none; line-height: 1;"></span>
                
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white text-black rounded-lg shadow-lg z-10 p-4 border border-gray-200" style="display: none;" role="dialog" aria-modal="true" aria-label="Notifications dropdown">
                    <p class="font-semibold mb-2 text-center">Notifications</p>
                    <div class="border-b border-black mb-2"></div>
                    <ul class="list-none" id="notification-list-dropdown">
                        <li>Loading notifications...</li>
                    </ul>
                    <div class="border-t border-black my-2"></div>
                    <a href="{{ route('admin.notifications') }}" class="block text-center text-green-600 hover:underline text-xs">View All Notifications</a>
                </div>
            </div>

            <!-- User menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none" aria-haspopup="true" :aria-expanded="open.toString()">
                    <i class="fas fa-user text-gray-900" aria-hidden="true"></i>
                    <span class="font-semibold">Admin Name</span>
                </button>
                <div 
                    x-show="open" 
                    @click.away="open = false" 
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg transition-opacity z-10 p-4"
                    style="display: none;"
                    role="menu" aria-label="User menu"
                >
                    <a href="{{ route('admin.profile') }}" 
                    class="block px-4 py-2 hover:bg-gray-200 rounded" 
                    role="menuitem" tabindex="-1">
                    Profile
                    </a>
                    <div class="border-t border-gray-200 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200 rounded" role="menuitem" tabindex="-1">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
        style="display: none;"
        aria-hidden="true"
    ></div>

    <div class="flex flex-grow overflow-x-hidden min-h-[calc(100vh-4rem)]">
        <!-- Desktop Sidebar -->
            <aside class="hidden md:block min-w-[17rem] text-gray-900 flex flex-col pt-5 px-1" aria-label="Sidebar navigation">
                <nav class="flex flex-col overflow-y-auto max-h-[calc(100vh-4rem)] px-2">
                    @php
                        // Helper to determine active states (optional)
                        function isActiveRoute($pattern) {
                            return request()->routeIs($pattern) ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300';
                        }
                    @endphp

                    <!-- Main section -->
                    <section class="mb-6" aria-label="Main navigation">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.dashboard') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.dashboard') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- User Management -->
                    <section class="mb-6" aria-label="User management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">User management</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.barangay-profiles') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.barangay-profiles*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.barangay-profiles*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-users fa-fw mr-3 {{ request()->routeIs('admin.barangay-profiles*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Barangay Profiles</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.residences') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.residences*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.residences*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residences*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Residence Information</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Reports & Requests -->
                    <section class="mb-6" aria-label="Reports & Requests">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Reports & Requests</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.blotter-reports') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.blotter-reports*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.blotter-reports*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('admin.blotter-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Blotter Reports</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.document-requests*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.document-requests*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Document Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.new-account-requests*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.new-account-requests*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.new-account-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Account Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Projects -->
                    <section class="mb-6" aria-label="Projects">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Projects</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.accomplished-projects') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.accomplished-projects*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.accomplished-projects*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-check-circle fa-fw mr-3 {{ request()->routeIs('admin.accomplished-projects*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Accomplished Projects</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Health Monitoring -->
                    <section class="mb-6" aria-label="Health Monitoring">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Monitoring</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-status') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.health-status*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.health-status*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-heartbeat fa-fw mr-3 {{ request()->routeIs('admin.health-status*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Status</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.health-reports') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.health-reports*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.health-reports*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-medical fa-fw mr-3 {{ request()->routeIs('admin.health-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Reports</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    
                </nav>
            </aside>

            <!-- Mobile Sidebar -->
            <aside aria-label="Mobile sidebar navigation"
                x-show="sidebarOpen"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                @click.stop
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-gray-900 flex flex-col pt-5 px-1 md:hidden"
                style="display:none"
            >
                <nav class="flex flex-col overflow-y-auto max-h-[calc(100vh-4rem)] px-2">
                    <!-- Duplicate all sections from desktop exactly -->

                    <!-- Main section -->
                    <section class="mb-6" aria-label="Main navigation">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- User Management -->
                    <section class="mb-6" aria-label="User management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">User management</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.barangay-profiles') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.barangay-profiles*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-users fa-fw mr-3 {{ request()->routeIs('admin.barangay-profiles*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Barangay Profiles</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.residences') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.residences*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residences*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Residence Information</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Reports & Requests -->
                    <section class="mb-6" aria-label="Reports & Requests">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Reports & Requests</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.blotter-reports') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.blotter-reports*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('admin.blotter-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Blotter Reports</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.document-requests*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Document Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.new-account-requests*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.new-account-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Account Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Projects -->
                    <section class="mb-6" aria-label="Projects">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Projects</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.accomplished-projects') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.accomplished-projects*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-check-circle fa-fw mr-3 {{ request()->routeIs('admin.accomplished-projects*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Accomplished Projects</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Health Monitoring -->
                    <section class="mb-6" aria-label="Health Monitoring">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Monitoring</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-status') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.health-status*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-heartbeat fa-fw mr-3 {{ request()->routeIs('admin.health-status*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Status</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.health-reports') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.health-reports*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-file-medical fa-fw mr-3 {{ request()->routeIs('admin.health-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Reports</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                </nav>
            </aside>

        <!-- Main content -->
        <main class="flex-grow p-6 overflow-auto">
            @yield('content')
        </main>
    </div>

    <!-- AlpineJS for menu toggle -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBadge = document.getElementById('notification-count-badge');
            const notificationListDropdown = document.getElementById('notification-list-dropdown');

            function updateNotifications() {
                fetch('{{ route('admin.notifications.count') }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update the badge
                        if (data.total > 0) {
                            notificationBadge.textContent = data.total;
                            notificationBadge.style.display = 'block';
                        } else {
                            notificationBadge.style.display = 'none';
                        }

                        // Update the dropdown list
                        let listHtml = '';
                        if (data.blotter_reports > 0) {
                            listHtml += `
                                <li class="text-center">
                                    <a href="{{ route('admin.blotter-reports') }}" class="block text-green-600 hover:bg-green-100 rounded-lg transition duration-200">
                                        <strong class="block">${data.blotter_reports} new blotter report(s)</strong>
                                        <small class="text-gray-500">${new Date().toLocaleDateString()}</small>
                                    </a>
                                </li>
                                <li class="border-t border-gray-100"></li>`;
                        }
                        if (data.document_requests > 0) {
                            listHtml += `
                                <li class="text-center">
                                    <a href="{{ route('admin.document-requests') }}" class="block text-green-600 hover:bg-green-100 rounded-lg transition duration-200">
                                        <strong class="block">${data.document_requests} new document request(s)</strong>
                                        <small class="text-gray-500">${new Date().toLocaleDateString()}</small>
                                    </a>
                                </li>
                                <li class="border-t border-gray-100"></li>`;
                        }
                        if (data.account_requests > 0) {
                            listHtml += `
                                <li class="text-center">
                                    <a href="{{ route('admin.new-account-requests') }}" class="block text-green-600 hover:bg-green-100 rounded-lg transition duration-200">
                                        <strong class="block">${data.account_requests} new account request(s)</strong>
                                        <small class="text-gray-500">${new Date().toLocaleDateString()}</small>
                                    </a>
                                </li>
                                <li class="border-t border-gray-100"></li>`;
                        }

                        if (listHtml === '') {
                            listHtml = '<li class="text-gray-500 text-center">No new notifications.</li>';
                        } else {
                            listHtml = listHtml.slice(0, -37); // Remove last border divider if there are items
                        }
                        notificationListDropdown.innerHTML = listHtml;


                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        notificationListDropdown.innerHTML = '<li>Error loading notifications.</li>';
                    });
            }

            updateNotifications();

            setInterval(updateNotifications, 30000);
        });
    </script>
</body>
</html>