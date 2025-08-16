<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Resident Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @notifyCss
    <style>
        .notify {
            z-index: 1001 !important;
        }
        /* Match admin notification dropdown scroll styling */
        .notification-scroll::-webkit-scrollbar { width: 6px; }
        .notification-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
        .notification-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .notification-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notify-timeout="{{ config('notify.timeout', 5000) }}" data-notifications-url="{{ route('resident.notifications.count') }}">
    @include('notify::components.notify')

    <!-- Header -->
    <header class="fixed top-0 left-0 w-full bg-white text-gray-900 flex items-center justify-between p-4 shadow-md z-50">
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
            <div class="relative"
                 x-data="{
                    open: false,
                    items: [],
                    loading: false,
                    load() {
                        const url = document.body.dataset.notificationsUrl || '{{ route('resident.notifications.count') }}';
                        this.loading = true;
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
                            .then(r => r.ok ? r.json() : Promise.reject())
                            .then(d => { this.items = Array.isArray(d.notifications) ? d.notifications : []; })
                            .catch(() => { this.items = []; })
                            .finally(() => { this.loading = false; });
                    },
                    init() {
                        this.load();
                        if (!window.__residentNotifInterval) {
                            window.__residentNotifInterval = setInterval(() => this.load(), 30000);
                            window.addEventListener('beforeunload', () => {
                                if (window.__residentNotifInterval) {
                                    clearInterval(window.__residentNotifInterval);
                                    window.__residentNotifInterval = null;
                                }
                            });
                        }
                    }
                 }"
                 x-init="init()"
                 @click.away="open = false">
                <button @click="open = !open" class="relative focus:outline-none" aria-label="Notifications" title="Notifications">
                    <i class="fas fa-bell text-gray-900"></i>
                    <span x-show="items.length > 0" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1" x-text="items.length"></span>
                </button>
                <div x-show="open"
                     x-transition
                     class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden"
                     style="display: none;"
                     role="dialog" aria-modal="true" aria-label="Notifications dropdown">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-white font-semibold text-sm">Notifications</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-white text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full" x-text="items.length"></span>
                                <button @click="open = false" class="text-white hover:text-gray-200 transition duration-200" aria-label="Close">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- List -->
                    <div class="max-h-96 overflow-y-auto notification-scroll">
                        <div class="p-4" x-show="loading">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                                <span class="ml-2 text-gray-500 text-sm">Loading notifications...</span>
                            </div>
                        </div>
                        <div class="p-2 divide-y divide-gray-100" x-show="!loading">
                            <template x-if="items.length === 0">
                                <div class="flex items-center justify-center py-8">
                                    <div class="text-center">
                                        <i class="fas fa-bell-slash text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-gray-500 text-sm">No new notifications</p>
                                    </div>
                                </div>
                            </template>
                            <template x-for="n in items" :key="n.id">
                                <div class="flex items-start p-3 hover:bg-gray-50 notification-item cursor-default select-none">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                        <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-900" x-text="n.message"></p>
                                        <p class="text-[10px] text-gray-500" x-text="new Date(n.created_at).toLocaleString()"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('resident.notifications') }}" class="text-green-600 hover:text-green-700 text-sm font-medium transition duration-200">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                View All Notifications
                            </a>
                            <form method="POST" action="{{ route('resident.notifications.mark-all') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm transition duration-200">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Mark All Read
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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

    <div class="flex flex-grow overflow-x-hidden min-h-[calc(100vh-4rem)] md:ml-[17rem] pt-16">
        <!-- Desktop Sidebar -->
            <aside class="hidden md:block fixed left-0 top-16 h-[calc(100vh-4rem)] min-w-[17rem] w-[17rem] bg-white shadow z-40 border-r border-gray-200 overflow-y-auto" aria-label="Sidebar navigation">
                <nav class="flex flex-col max-h-full px-2 pt-10">
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

                    <!-- Community Concern -->
                    <section class="mb-6" aria-label="Concern">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Concern</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.request_community_concern') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_community_concern') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.request_community_concern') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('resident.request_community_concern') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Community Concern</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    <div class="flex-shrink-0 h-12"></div>
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

                    <!-- Community Concern -->
                    <section class="mb-6" aria-label="Concern">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Concern</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.request_community_concern') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.request_community_concern') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.request_community_concern') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('resident.request_community_concern') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Community Concern</span>
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