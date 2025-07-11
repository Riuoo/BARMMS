<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @notifyCss
    <style type="text/css">
        .notify {
            z-index: 1001 !important;
        }
        
        /* Custom notification styles */
        .notification-item {
            transition: all 0.2s ease-in-out;
        }
        
        .notification-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        /* Smooth transitions for notification dropdown */
        .notification-dropdown-enter {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        
        .notification-dropdown-enter-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: opacity 200ms ease-out, transform 200ms ease-out;
        }
        
        .notification-dropdown-leave {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        
        .notification-dropdown-leave-active {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
            transition: opacity 150ms ease-in, transform 150ms ease-in;
        }
        
        /* Custom scrollbar for notification dropdown */
        .notification-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .notification-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .notification-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .notification-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notifications-url="{{ route('admin.notifications.count') }}" data-notify-timeout="{{ config('notify.timeout', 5000) }}">
    @include('notify::components.notify')

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
            <div class="relative" x-data="{ open: false }" @click="open = !open">
                <button class="relative p-2 text-gray-900 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-lg transition duration-200" title="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" role="img" aria-label="Notifications Icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{-- Notification count badge --}}
                    <span id="notification-count-badge" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs font-medium animate-pulse" style="display: none;"></span>
                </button>

                <div x-show="open" 
                     @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden" 
                     style="display: none;" 
                     role="dialog" 
                     aria-modal="true" 
                     aria-label="Notifications dropdown">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-white font-semibold text-sm">Notifications</h3>
                            <div class="flex items-center space-x-2">
                                <span id="dropdown-notification-count" class="text-white text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">0</span>
                                <button @click="open = false" class="text-white hover:text-gray-200 transition duration-200">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="max-h-96 overflow-y-auto notification-scroll">
                        <div id="notification-list-dropdown" class="p-4">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                                <span class="ml-2 text-gray-500 text-sm">Loading notifications...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.notifications') }}" class="text-green-600 hover:text-green-700 text-sm font-medium transition duration-200">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                View All Notifications
                            </a>
                            <button onclick="markAllAsRead()" class="text-gray-600 hover:text-gray-800 text-sm transition duration-200">
                                <i class="fas fa-check-double mr-1"></i>
                                Mark All Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none" aria-haspopup="true" :aria-expanded="open.toString()">
                    <i class="fas fa-user text-gray-900" aria-hidden="true"></i>
                    <span class="font-semibold">
                        @if($currentAdminUser)
                            {{ $currentAdminUser->name }}
                        @else
                            Admin
                        @endif
                    </span>
                </button>
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg transition-opacity z-50 p-4"
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
                    @if(session('user_role') === 'barangay')
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
                                <a href="{{ route('admin.residents') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.residents*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.residents*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residents*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Information</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

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
                    @if(session('user_role') === 'barangay')
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
                                <a href="{{ route('admin.residents') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.residents*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residents*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Information</span>
                                </a>
                            </a>
                            </li>
                        </ul>
                    </section>
                    @endif

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
    <!-- Custom notification auto-removal system -->
    <script>
        // Auto-remove notifications after timeout (non-conflicting with Alpine.js)
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notify');
            const timeout = parseInt(document.body.dataset.notifyTimeout) || 5000; // Get from data attribute or default to 5 seconds
            
            notifications.forEach(notification => {
                // Add click to dismiss functionality
                notification.addEventListener('click', function() {
                    fadeOutAndRemove(notification);
                });
                
                // Auto-remove after timeout
                setTimeout(() => {
                    fadeOutAndRemove(notification);
                }, timeout);
            });
            
            // Helper function to fade out and remove notification
            function fadeOutAndRemove(notification) {
                if (notification && notification.parentNode) {
                    // Add fade-out effect
                    notification.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    
                    // Remove after fade animation
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 500);
                }
            }
        });
    </script>
</body>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const notificationBadge = document.getElementById('notification-count-badge');
            const notificationListDropdown = document.getElementById('notification-list-dropdown');
            const notificationsUrl = document.body.dataset.notificationsUrl;

            // --- NEW FUNCTION: timeAgo ---
            function timeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);

                let interval = seconds / 31536000; // seconds in a year
                if (interval > 1) {
                    return Math.floor(interval) + " year" + (Math.floor(interval) === 1 ? "" : "s") + " ago";
                }
                interval = seconds / 2592000; // seconds in a month
                if (interval > 1) {
                    return Math.floor(interval) + " month" + (Math.floor(interval) === 1 ? "" : "s") + " ago";
                }
                interval = seconds / 86400; // seconds in a day
                if (interval > 1) {
                    return Math.floor(interval) + " day" + (Math.floor(interval) === 1 ? "" : "s") + " ago";
                }
                interval = seconds / 3600; // seconds in an hour
                if (interval > 1) {
                    return Math.floor(interval) + " hour" + (Math.floor(interval) === 1 ? "" : "s") + " ago";
                }
                interval = seconds / 60; // seconds in a minute
                if (interval > 1) {
                    return Math.floor(interval) + " minute" + (Math.floor(interval) === 1 ? "" : "s") + " ago";
                }
                return Math.floor(seconds) + " second" + (Math.floor(seconds) === 1 ? "" : "s") + " ago";
            }
            // --- END NEW FUNCTION ---


            function updateNotifications() {
                fetch(notificationsUrl)
                    .then(response => {
                        if (!response.ok) {
                            console.error('Network response was not ok:', response.status, response.statusText);
                            return response.text().then(text => {
                                console.error('Response body:', text);
                                throw new Error('Server responded with status ' + response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('API Response Data:', data);

                        // Update the badge
                        if (data.total > 0) {
                            notificationBadge.textContent = data.total;
                            notificationBadge.style.display = 'flex';
                        } else {
                            notificationBadge.style.display = 'none';
                        }

                        // Update dropdown count
                        const dropdownCount = document.getElementById('dropdown-notification-count');
                        if (dropdownCount) {
                            dropdownCount.textContent = data.total;
                        }

                        // Update the dropdown list with individual notifications
                        let listHtml = '';
                        if (Array.isArray(data.notifications) && data.notifications.length > 0) {
                            data.notifications.forEach(notification => {
                                const iconClass = getNotificationIcon(notification.type);
                                const typeLabel = getNotificationTypeLabel(notification.type);
                                
                                listHtml += `
                                    <div class="notification-item mb-3 p-3 bg-white border border-gray-200 rounded-lg hover:shadow-md transition duration-200 cursor-pointer"
                                         data-type="${notification.type}" data-id="${notification.id}">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 ${iconClass.bg} rounded-full flex items-center justify-center">
                                                    <i class="${iconClass.icon} text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 leading-tight">
                                                    ${notification.message}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    ${typeLabel} • ${timeAgo(notification.created_at)}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    New
                                                </span>
                                            </div>
                                        </div>
                                    </div>`;
                            });
                        } else {
                            listHtml = `
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-bell text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No new notifications</p>
                                    <p class="text-gray-400 text-xs mt-1">You're all caught up!</p>
                                </div>`;
                        }
                        notificationListDropdown.innerHTML = listHtml;

                        // Attach event listeners to the new notification items
                        attachNotificationItemListeners();

                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        notificationListDropdown.innerHTML = `
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                </div>
                                <p class="text-red-500 text-sm">Error loading notifications</p>
                                <p class="text-gray-400 text-xs mt-1">Please try again later</p>
                            </div>`;
                    });
            }

            function getNotificationIcon(type) {
                switch(type) {
                    case 'blotter_report':
                        return { icon: 'fas fa-file-alt text-red-600', bg: 'bg-red-100' };
                    case 'document_request':
                        return { icon: 'fas fa-file-signature text-blue-600', bg: 'bg-blue-100' };
                    case 'account_request':
                        return { icon: 'fas fa-user-plus text-green-600', bg: 'bg-green-100' };
                    default:
                        return { icon: 'fas fa-bell text-gray-600', bg: 'bg-gray-100' };
                }
            }

            function getNotificationTypeLabel(type) {
                switch(type) {
                    case 'blotter_report':
                        return 'Blotter Report';
                    case 'document_request':
                        return 'Document Request';
                    case 'account_request':
                        return 'Account Request';
                    default:
                        return 'Notification';
                }
            }

            function attachNotificationItemListeners() {
                document.querySelectorAll('.notification-item').forEach(item => {
                    // Remove existing listener to prevent multiple calls
                    item.removeEventListener('click', handleNotificationItemClick);
                    // Add new listener
                    item.addEventListener('click', handleNotificationItemClick);
                });
            }

            function handleNotificationItemClick(event) {
                const item = event.currentTarget;
                const type = item.dataset.type;
                const id = item.dataset.id;

                if (type && id) {
                    // Send AJAX request to mark as read
                    fetch(`/admin/notifications/mark-as-read/${type}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data.message);
                        // After marking as read, update the notifications list
                        updateNotifications();
                        // Navigate to the appropriate page based on notification type
                        navigateToNotificationPage(type, id);
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                        // Even if marking fails, still navigate
                        navigateToNotificationPage(type, id);
                    });
                }
            }

            function navigateToNotificationPage(type, id) {
                let url = '';
                switch(type) {
                    case 'blotter_report':
                        url = '/admin/blotter-reports';
                        break;
                    case 'document_request':
                        url = '/admin/document-requests';
                        break;
                    case 'account_request':
                        url = '/admin/new-account-requests';
                        break;
                    default:
                        url = '/admin/notifications';
                }
                window.location.href = url;
            }

            function markAllAsRead() {
                fetch('/admin/notifications/mark-all-as-read-ajax', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }

                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('All notifications marked as read');
                        updateNotifications();
                        
                        // Show success message
                        const notificationBadge = document.getElementById('notification-count-badge');
                        if (notificationBadge) {
                            notificationBadge.style.display = 'none';
                        }
                        
                        const dropdownCount = document.getElementById('dropdown-notification-count');
                        if (dropdownCount) {
                            dropdownCount.textContent = '0';
                        }
                    } else {
                        console.error('Failed to mark all notifications as read:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
            }

            updateNotifications();

            // Re-fetch notifications every 30 seconds
            setInterval(updateNotifications, 30000);
        });
    </script>
</html>