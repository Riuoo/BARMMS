<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Resident Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @notifyCss
    <style>
        .notify {
            z-index: 1001 !important;
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notify-timeout="{{ config('notify.timeout', 5000) }}">
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
            <!-- User menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none" aria-haspopup="true" :aria-expanded="open.toString()">
                    <i class="fas fa-user text-gray-900" aria-hidden="true"></i>
                    <span class="font-semibold hidden sm:inline">{{ $currentUser->name ?? 'Resident' }}</span>
                </button>
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg transition-opacity z-50 p-4"
                    style="display: none;"
                    role="menu" aria-label="User menu"
                >
                    <a href="{{ route('resident.profile') }}"
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
                        function isActiveResidentRoute($pattern) {
                            return request()->routeIs($pattern) ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300';
                        }
                    @endphp

                    <!-- Main section -->
                    <section class="mb-6" aria-label="Main navigation">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.dashboard') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.dashboard') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('resident.dashboard') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Requests -->
                    <section class="mb-6" aria-label="Requests">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Requests</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.request_blotter_report') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_blotter_report') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.request_blotter_report') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('resident.request_blotter_report') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Request a Blotter</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.request_community_complaint') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_community_complaint') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.request_community_complaint') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('resident.request_community_complaint') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Community Complaint</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.request_document_request') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_document_request') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.request_document_request') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('resident.request_document_request') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Request a Document</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.my-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.my-requests') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.my-requests') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('resident.my-requests') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>My Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Health Monitoring (Recommendation) -->
                    <section class="mb-6" aria-label="Health Monitoring">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Monitoring</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.health-status') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.health-status') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.health-status') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-heartbeat fa-fw mr-3 {{ request()->routeIs('resident.health-status') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Report Health Concerns</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Information & Profile -->
                    <section class="mb-6" aria-label="Information & Profile">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Information & Profile</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.announcements') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.announcements') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.announcements') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-bullhorn fa-fw mr-3 {{ request()->routeIs('resident.announcements') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Announcements</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.profile') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.profile') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.profile') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-user-circle fa-fw mr-3 {{ request()->routeIs('resident.profile') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>My Profile</span>
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
                style="display:none">
                <nav class="flex flex-col overflow-y-auto max-h-[calc(100vh-4rem)] px-2">
                    <!-- Duplicate all sections from desktop exactly -->

                    <!-- Main section -->
                    <section class="mb-6" aria-label="Main navigation">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.dashboard') }} transition duration-300 text-base">
                                    <i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('resident.dashboard') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Requests -->
                    <section class="mb-6" aria-label="Requests">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Requests</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.request_blotter_report') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_blotter_report') }} transition duration-300 text-base">
                                    <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('resident.request_blotter_report') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>New Blotter Report</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.request_document_request') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_document_request') }} transition duration-300 text-base">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('resident.request_document_request') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>New Document Request</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.my-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.my-requests') }} transition duration-300 text-base">
                                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('resident.my-requests') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>My Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Health Monitoring (Recommendation) -->
                    <section class="mb-6" aria-label="Health Monitoring">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Monitoring</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.health-status') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.health-status') }} transition duration-300 text-base">
                                    <i class="fas fa-heartbeat fa-fw mr-3 {{ request()->routeIs('resident.health-status') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Report Health Concerns</span>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <!-- Information & Profile -->
                    <section class="mb-6" aria-label="Information & Profile">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Information & Profile</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.announcements') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.announcements') }} transition duration-300 text-base">
                                    <i class="fas fa-bullhorn fa-fw mr-3 {{ request()->routeIs('resident.announcements') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Announcements</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.profile') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.profile') }} transition duration-300 text-base">
                                    <i class="fas fa-user-circle fa-fw mr-3 {{ request()->routeIs('resident.profile') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>My Profile</span>
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
</html>