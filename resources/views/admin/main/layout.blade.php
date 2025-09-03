<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
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

        /* Enhanced dropdown styles */
        .user-dropdown {
            z-index: 9999 !important;
        }

        .user-dropdown-menu {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }

        /* Prevent text selection in dropdown */
        .user-dropdown-menu * {
            user-select: none;
        }

        /* Smooth hover transitions */
        .user-dropdown-menu a:hover,
        .user-dropdown-menu button:hover {
            transform: translateX(2px);
        }

        /* Notification dropdown styles */
        .notification-dropdown {
            z-index: 9998 !important;
        }

        .notification-dropdown .notification-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .notification-dropdown .notification-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .notification-dropdown .notification-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 3px;
        }

        .notification-dropdown .notification-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        .notification-dropdown .notification-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notifications-url="{{ route('admin.notifications.count') }}" data-notify-timeout="{{ config('notify.timeout', 5000) }}">
    @include('notify::components.notify')

    @php
        // Global role helpers for layout and scripts
        $userRole = session('user_role');
        $isNurse = $userRole === 'nurse';
        $isAdmin = $userRole === 'admin';
        $isTreasurer = $userRole === 'treasurer';
        $isSecretary = $userRole === 'secretary';
        $isCaptain = $userRole === 'captain';
        $isCouncilor = $userRole === 'councilor';
        
        // Only admin and secretary can perform transactions (create, edit, delete)
        $canPerformTransactions = $isAdmin || $isSecretary;
        
        // All non-nurse roles can view Reports & Requests sections
        $canViewReportsRequests = !$isNurse;
    @endphp

    <!-- Enhanced Toast Notification System -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[9998] space-y-2"></div>

    <script>
        // Enhanced Toast Notification System
        window.toast = {
            show: function(message, type = 'info', duration = 5000) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                
                const colors = {
                    success: 'bg-green-500 border-green-600',
                    error: 'bg-red-500 border-red-600',
                    warning: 'bg-yellow-500 border-yellow-600',
                    info: 'bg-blue-500 border-blue-600'
                };
                
                const icons = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-circle',
                    warning: 'fas fa-exclamation-triangle',
                    info: 'fas fa-info-circle'
                };
                
                toast.className = `flex items-center p-4 rounded-lg shadow-lg border-l-4 text-white transform translate-x-full transition-all duration-300 ${colors[type] || colors.info}`;
                toast.innerHTML = `
                    <i class="${icons[type] || icons.info} mr-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                container.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);
                
                // Auto remove
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }, duration);
            },
            
            success: function(message, duration) {
                this.show(message, 'success', duration);
            },
            
            error: function(message, duration) {
                this.show(message, 'error', duration);
            },
            
            warning: function(message, duration) {
                this.show(message, 'warning', duration);
            },
            
            info: function(message, duration) {
                this.show(message, 'info', duration);
            }
        };

        // Provide a fallback notify only after page load, and only if not provided by a library
        window.addEventListener('load', function() {
            if (typeof window.notify !== 'function') {
                window.notify = function(type, message) {
                    window.toast[type](message);
                };
            }
        });
    </script>

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
            @if(!$isNurse)
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                <button 
                    @click="open = !open" 
                    class="relative focus:outline-none rounded-md p-1" 
                    title="Notifications"
                    type="button"
                >
                    <i class="fas fa-bell text-gray-900"></i>
                    {{-- Notification count badge --}}
                    <span id="notification-count-badge" class="absolute -top-0.5 -right-1 bg-red-600 text-white text-xs rounded-full px-0.5 min-w-[16px] h-4 flex items-center justify-center" style="display: none;"></span>
                </button>

                <div x-show="open" 
                     @click.away="setTimeout(() => open = false, 100)" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden notification-dropdown" 
                     style="display: none;" 
                     role="dialog" 
                     aria-modal="true" 
                     aria-label="Notifications dropdown"
                     @click.stop>
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-white font-semibold text-sm">
                                @if($isNurse)
                                    Health Notifications
                                @else
                                    Notifications
                                @endif
                            </h3>
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
                                <span class="ml-2 text-gray-500 text-sm">
                                    @if($isNurse)
                                        Loading health notifications...
                                    @else
                                        Loading notifications...
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            @if(!$isNurse)
                            <a href="{{ route('admin.notifications') }}" class="text-green-600 hover:text-green-700 text-sm font-medium transition duration-200">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                View All Notifications
                            </a>
                            @else
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Health notifications only
                            </span>
                            @endif
                            <button onclick="markAllAsRead()" class="text-gray-600 hover:text-gray-800 text-sm transition duration-200">
                                <i class="fas fa-check-double mr-1"></i>
                                Mark All Read
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- User menu -->
            <div class="relative user-dropdown" x-data="{ open: false }" @keydown.escape="open = false">
                <button 
                    @click="open = !open" 
                    @click.away="setTimeout(() => open = false, 100)"
                    class="flex items-center space-x-2 focus:outline-none rounded-md p-1" 
                    aria-haspopup="true" 
                    :aria-expanded="open.toString()"
                    type="button"
                >
                    <i class="fas fa-user text-gray-900" aria-hidden="true"></i>
                    <span class="font-semibold hidden sm:inline">
                        @php
                            $currentAdminUser = null;
                            if (session('user_role') === 'barangay' || session('user_id')) {
                                $currentAdminUser = \App\Models\BarangayProfile::find(session('user_id'));
                            }
                        @endphp
                        @if($currentAdminUser)
                            {{ $currentAdminUser->name }}
                        @else
                            Admin
                        @endif
                    </span>

                </button>
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white text-black rounded-lg shadow-xl border border-gray-200 transition-all z-50 overflow-hidden user-dropdown-menu"
                    style="display: none;"
                    role="menu" 
                    aria-label="User menu"
                    @click.stop
                >
                    <div class="py-1">
                        <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors duration-150 flex items-center"
                        role="menuitem" 
                        tabindex="-1"
                        @click="open = false">
                            <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                            Profile
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 flex items-center" 
                                role="menuitem" 
                                tabindex="-1"
                                @click="open = false">
                                <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                Logout
                            </button>
                        </form>
                    </div>
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
                        // Helper to determine active states (optional)
                        function isActiveRoute($pattern) {
                            return request()->routeIs($pattern) ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300';
                        }
                    @endphp

                    @if(!$isNurse)
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
                    @endif

                    @if(!$isNurse)
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
                                <a href="{{ route('admin.residents') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.residents*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.residents*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residents*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Information</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if(!$isNurse && !$isTreasurer)
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
                                                <a href="{{ route('admin.community-concerns') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.community-concerns*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.community-concerns*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('admin.community-concerns*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                    <span>Community Concerns</span>
                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.document-requests*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.document-requests*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Document Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.requests.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.requests.new-account-requests*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.requests.new-account-requests*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.requests.new-account-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Account Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isTreasurer || $isAdmin)
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
                    @endif
                    
                    @if($isNurse || $isAdmin)
                    <!-- Main Health Section -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-reports') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.health-reports*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.health-reports*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-chart-line fa-fw mr-3 {{ request()->routeIs('admin.health-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isNurse || $isAdmin)
                    <!-- Health Management -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Management</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.vaccination-records.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.vaccination-records*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.vaccination-records*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-syringe fa-fw mr-3 {{ request()->routeIs('admin.vaccination-records*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Vaccination Records</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medical-records.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.medical-records*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.medical-records*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-stethoscope fa-fw mr-3 {{ request()->routeIs('admin.medical-records*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medical Records</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicines.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.medicines*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.medicines*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-pills fa-fw mr-3 {{ request()->routeIs('admin.medicines*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicines Inventory</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicine-requests.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.medicine-requests*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.medicine-requests*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-clipboard-check fa-fw mr-3 {{ request()->routeIs('admin.medicine-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicine Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicine-transactions.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.medicine-transactions*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.medicine-transactions*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-list fa-fw mr-3 {{ request()->routeIs('admin.medicine-transactions*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicine Transactions</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isNurse || $isAdmin)
                    <!-- Main Health Section -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Activities</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-center-activities.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.health-center-activities*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-calendar-alt fa-fw mr-3 {{ request()->routeIs('admin.health-center-activities*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Activities</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if(!$isNurse)
                    <!-- Analytics -->
                    <section class="mb-6" aria-label="Analytics">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Analytics</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.clustering') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.clustering*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.clustering*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-chart-pie fa-fw mr-3 {{ request()->routeIs('admin.clustering*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Demographic Analysis</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.decision-tree') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.decision-tree*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.decision-tree*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-sitemap fa-fw mr-3 {{ request()->routeIs('admin.decision-tree*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Classification & Prediction</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif
                    
                </nav>
                <div class="flex-shrink-0 h-12"></div>
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
                    <!-- Duplicate all sections from desktop with role-based access control -->

                    @if(!$isNurse)
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
                    @endif

                    @if(!$isNurse)
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
                                <a href="{{ route('admin.residents') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.residents*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-home fa-fw mr-3 {{ request()->routeIs('admin.residents*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Information</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if(!$isNurse && !$isTreasurer)
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
                                                <a href="{{ route('admin.community-concerns') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.community-concerns*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                    <i class="fas fa-clipboard-list fa-fw mr-3 {{ request()->routeIs('admin.community-concerns*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                    <span>Community Concerns</span>
                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.document-requests*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Document Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.requests.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.requests.new-account-requests*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.requests.new-account-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Account Requests</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isTreasurer || $isAdmin)
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
                    @endif

                    @if($isNurse || $isAdmin)
                    <!-- Main Health Section -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-reports') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.health-reports*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.health-reports*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-chart-line fa-fw mr-3 {{ request()->routeIs('admin.health-reports*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isNurse || $isAdmin)
                    <!-- Health Management -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Health Management</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.vaccination-records.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.vaccination-records*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-syringe fa-fw mr-3 {{ request()->routeIs('admin.vaccination-records*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Vaccination Records</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medical-records.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.medical-records*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-stethoscope fa-fw mr-3 {{ request()->routeIs('admin.medical-records*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medical Records</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicines.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.medicines*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-pills fa-fw mr-3 {{ request()->routeIs('admin.medicines*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicines Inventory</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicine-requests.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.medicine-requests*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-clipboard-check fa-fw mr-3 {{ request()->routeIs('admin.medicine-requests*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicine Requests</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.medicine-transactions.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.medicine-transactions*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-list fa-fw mr-3 {{ request()->routeIs('admin.medicine-transactions*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Medicine Transactions</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if($isNurse || $isAdmin)
                    <!-- Main Health Section -->
                    <section class="mb-6" aria-label="Health Management">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Activities</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.health-center-activities.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.health-center-activities*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-calendar-alt fa-fw mr-3 {{ request()->routeIs('admin.health-center-activities*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Health Activities</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif

                    @if(!$isNurse)
                    <!-- Analytics -->
                    <section class="mb-6" aria-label="Analytics">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Analytics</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.clustering') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.clustering*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-chart-pie fa-fw mr-3 {{ request()->routeIs('admin.clustering*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Demographic Analysis</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.decision-tree') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.decision-tree*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-sitemap fa-fw mr-3 {{ request()->routeIs('admin.decision-tree*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Resident Classification & Prediction</span>
                                </a>
                            </li>
                        </ul>
                    </section>
                    @endif
                </nav>
                <div class="flex-shrink-0 h-12"></div>
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

    <!-- Global Loading Overlay -->
    <div id="globalLoadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
        <div class="bg-white rounded-lg p-6 flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-4"></div>
            <p class="text-gray-700 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Global Error Handler -->
    <script>
        // Global loading functions
        window.showGlobalLoading = function() {
            document.getElementById('globalLoadingOverlay').classList.remove('hidden');
            document.getElementById('globalLoadingOverlay').classList.add('flex');
        };

        window.hideGlobalLoading = function() {
            document.getElementById('globalLoadingOverlay').classList.add('hidden');
            document.getElementById('globalLoadingOverlay').classList.remove('flex');
        };

        // Global error handler
        window.handleGlobalError = function(error, context = '') {
            console.error('Global Error:', error);
            const message = context ? `${context}: ${error.message || error}` : error.message || error;
            
            if (typeof notify !== 'undefined') {
                notify('error', message);
            } else {
                alert('Error: ' + message);
            }
        };

        // Intercept form submissions for loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const submitBtn = form.querySelector('button[type="submit"]');
                
                if (submitBtn && !form.classList.contains('no-loading')) {
                    // Show loading state
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    submitBtn.disabled = true;
                    
                    // Hide loading after a reasonable timeout
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 10000); // 10 second timeout
                }
            });

            // Handle all link clicks for loading states
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.classList.contains('no-loading')) {
                    const isExternal = link.hostname !== window.location.hostname;
                    if (!isExternal) {
                        link.addEventListener('click', function() {
                            showGlobalLoading();
                        });
                    }
                }
            });
        });

        // Handle AJAX errors globally
        window.addEventListener('unhandledrejection', function(event) {
            handleGlobalError(event.reason, 'Network Error');
        });

        // Enhanced Form Validation
        window.validateForm = function(form) {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalidInput = null;

            inputs.forEach(input => {
                const errorElement = input.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.remove();
                }

                if (!input.value.trim()) {
                    isValid = false;
                    if (!firstInvalidInput) firstInvalidInput = input;
                    
                    // Add error styling
                    input.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    input.classList.remove('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
                    
                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-600 text-sm mt-1';
                    errorDiv.textContent = `${input.placeholder || input.name} is required`;
                    input.parentNode.appendChild(errorDiv);
                } else {
                    // Remove error styling
                    input.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    input.classList.add('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
                }
            });

            if (!isValid && firstInvalidInput) {
                firstInvalidInput.focus();
                toast.error('Please fill in all required fields.');
            }

            return isValid;
        };

        // Real-time form validation
        document.addEventListener('input', function(e) {
            const input = e.target;
            if (input.hasAttribute('required')) {
                const errorElement = input.parentNode.querySelector('.error-message');
                
                if (input.value.trim()) {
                    // Remove error styling
                    input.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    input.classList.add('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
                    
                    if (errorElement) {
                        errorElement.remove();
                    }
                } else {
                    // Add error styling
                    input.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                    input.classList.remove('border-gray-300', 'focus:ring-green-500', 'focus:border-green-500');
                    
                    if (!errorElement) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-red-600 text-sm mt-1';
                        errorDiv.textContent = `${input.placeholder || input.name} is required`;
                        input.parentNode.appendChild(errorDiv);
                    }
                }
            }
        });

        // Enhanced form submission
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            if (form.classList.contains('enhanced-form')) {
                if (!validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    submitBtn.disabled = true;
                    
                    // Re-enable after timeout
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 10000);
                }
            }
        });
    </script>

    <!-- Enhanced Search & Filter System -->
    <script>
        // Global search functionality
        window.enhancedSearch = {
            init: function(containerId, options = {}) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const searchInput = container.querySelector('.search-input');
                const filterButtons = container.querySelectorAll('.filter-btn');
                const items = container.querySelectorAll('.searchable-item');
                const noResultsElement = container.querySelector('.no-results');

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        let hasResults = false;

                        items.forEach(item => {
                            const text = item.textContent.toLowerCase();
                            const shouldShow = text.includes(searchTerm);
                            
                            if (shouldShow) hasResults = true;
                            item.style.display = shouldShow ? '' : 'none';
                        });

                        if (noResultsElement) {
                            noResultsElement.style.display = hasResults ? 'none' : 'block';
                        }
                    });
                }

                if (filterButtons.length > 0) {
                    filterButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const filter = this.dataset.filter;
                            
                            // Update active button
                            filterButtons.forEach(b => {
                                b.classList.remove('active', 'bg-green-100', 'text-green-800');
                                b.classList.add('bg-gray-100', 'text-gray-600');
                            });
                            this.classList.add('active', 'bg-green-100', 'text-green-800');
                            this.classList.remove('bg-gray-100', 'text-gray-600');
                            
                            // Apply filter
                            items.forEach(item => {
                                const itemFilter = item.dataset.filter || item.dataset.category;
                                if (filter === 'all' || itemFilter === filter) {
                                    item.style.display = '';
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                        });
                    });
                }
            },

            // Advanced search with multiple criteria
            advancedSearch: function(containerId, criteria) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const items = container.querySelectorAll('.searchable-item');
                const searchTerm = criteria.searchTerm?.toLowerCase() || '';
                const filters = criteria.filters || {};
                const sortBy = criteria.sortBy || 'name';
                const sortOrder = criteria.sortOrder || 'asc';

                items.forEach(item => {
                    let shouldShow = true;

                    // Text search
                    if (searchTerm) {
                        const text = item.textContent.toLowerCase();
                        shouldShow = shouldShow && text.includes(searchTerm);
                    }

                    // Filter criteria
                    Object.keys(filters).forEach(key => {
                        if (filters[key] && filters[key] !== 'all') {
                            const itemValue = item.dataset[key];
                            shouldShow = shouldShow && itemValue === filters[key];
                        }
                    });

                    item.style.display = shouldShow ? '' : 'none';
                });

                // Sort results
                if (sortBy) {
                    const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
                    visibleItems.sort((a, b) => {
                        const aValue = a.dataset[sortBy] || a.textContent;
                        const bValue = b.dataset[sortBy] || b.textContent;
                        
                        if (sortOrder === 'asc') {
                            return aValue.localeCompare(bValue);
                        } else {
                            return bValue.localeCompare(aValue);
                        }
                    });

                    // Reorder in DOM
                    const parent = visibleItems[0]?.parentNode;
                    if (parent) {
                        visibleItems.forEach(item => {
                            parent.appendChild(item);
                        });
                    }
                }
            }
        };

        // Initialize search on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-initialize search on containers with search-input class
            document.querySelectorAll('.search-container').forEach(container => {
                enhancedSearch.init(container.id);
            });
        });
    </script>

    <!-- Notification System JavaScript -->
    <script>
        // Global notification system
        window.notificationSystem = {
            // Initialize notification system
            init: function() {
                this.loadNotifications();
                this.startPolling();
                this.bindEvents();
                this.checkCurrentPageAndMarkNotifications();
            },

            // Load notifications from server
            loadNotifications: function() {
                const url = document.body.dataset.notificationsUrl;
                if (!url) {
                    console.error('Notification URL not found');
                    return;
                }

                console.log('Loading notifications from:', url);

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Notification response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Notification data received:', data);
                    this.updateNotificationBadge(data.total);
                    this.updateNotificationDropdown(data.notifications);
                    this.updateNotificationCount(data.total);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
            },

            // Update notification badge
            updateNotificationBadge: function(count) {
                const badge = document.getElementById('notification-count-badge');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            },

            // Update notification dropdown
            updateNotificationDropdown: function(notifications) {
                const container = document.getElementById('notification-list-dropdown');
                if (!container) return;

                                        if (notifications.length === 0) {
                            const isNurse = JSON.parse(`@json($isNurse)`);
                            
                            if (isNurse) {
                                container.innerHTML = `
                                    <div class="flex items-center justify-center py-8">
                                        <div class="text-center">
                                            <i class="fas fa-heartbeat text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-gray-500 text-sm">Health notifications only</p>
                                            <p class="text-gray-400 text-xs mt-1">No new health-related notifications</p>
                                        </div>
                                    </div>
                                `;
                            } else {
                                container.innerHTML = `
                                    <div class="flex items-center justify-center py-8">
                                        <div class="text-center">
                                            <i class="fas fa-bell-slash text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-gray-500 text-sm">No new notifications</p>
                                        </div>
                                    </div>
                                `;
                            }
                            return;
                        }

                let html = '';
                notifications.forEach(notification => {
                    const timeAgo = this.getTimeAgo(notification.created_at);
                    const priorityClass = notification.priority === 'high' ? 'border-l-4 border-red-500' : 'border-l-4 border-blue-500';
                    
                    html += `
                        <div class="flex items-center justify-center p-3 hover:bg-gray-50 notification-item cursor-default select-none ${priorityClass}" data-id="${notification.id}" data-type="${notification.type}" onclick="notificationSystem.markAsViewed(${notification.id}, '${notification.type}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <button onclick="event.stopPropagation(); notificationSystem.viewDetails('${notification.type}', ${notification.id})" class="text-gray-400 hover:text-blue-600 transition duration-200" title="View details">
                                        <p class="text-s text-gray-900">${notification.message}</p>
                                        <p class="text-[15px] text-gray-500">${timeAgo}</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            },

            // Update notification count in dropdown header
            updateNotificationCount: function(count) {
                const countElement = document.getElementById('dropdown-notification-count');
                if (countElement) {
                    countElement.textContent = count;
                }
            },

            // Mark notification as read
            markAsRead: function(type, id) {
                // Check if user is a nurse
                const isNurse = JSON.parse(`@json($isNurse)`);
                
                if (isNurse) {
                    toast.info('Nurses can only access health-related notifications');
                    return;
                }
                
                // Get notification message for confirmation
                const notificationElement = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
                const messageElement = notificationElement?.querySelector('p');
                const notificationMessage = messageElement ? messageElement.textContent : 'this notification';
                
                // Show confirmation dialog
                if (!confirm(`Are you sure you want to mark "${notificationMessage}" as read?\n\nThis action cannot be undone.`)) {
                    return;
                }
                
                fetch(`/admin/notifications/mark-as-read/${type}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    // Remove the notification from the dropdown
                    if (notificationElement) {
                        notificationElement.style.opacity = '0.5';
                        setTimeout(() => {
                            notificationElement.remove();
                            this.loadNotifications(); // Reload to update counts
                        }, 300);
                    }
                    toast.success('Notification marked as read');
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    toast.error('Failed to mark notification as read');
                });
            },

            // Mark all notifications as read
            markAllAsRead: function() {
                // Check if user is a nurse
                const isNurse = JSON.parse(`@json($isNurse)`);
                
                if (isNurse) {
                    toast.info('Nurses can only access health-related notifications');
                    return;
                }
                
                // Get current notification count
                const currentCount = document.getElementById('dropdown-notification-count')?.textContent || '0';
                
                // Show confirmation dialog
                if (!confirm(`Are you sure you want to mark all ${currentCount} notifications as read?\n\nThis action cannot be undone and will mark ALL unread notifications as read.`)) {
                    return;
                }
                
                fetch('/admin/notifications/mark-all-as-read-ajax', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.updateNotificationBadge(0);
                        this.updateNotificationCount(0);
                        this.updateNotificationDropdown([]);
                        toast.success(data.message);
                    } else {
                        toast.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                    toast.error('Failed to mark all notifications as read');
                });
            },

            // Mark notification as viewed (but not read)
            markAsViewed: function(id, type) {
                // Add a subtle visual indicator that this notification has been viewed
                const notificationElement = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
                if (notificationElement) {
                    notificationElement.classList.add('viewed');
                    notificationElement.style.opacity = '0.8';
                    // Add a small indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full';
                    notificationElement.style.position = 'relative';
                    notificationElement.appendChild(indicator);
                }
            },

            // View notification details
            viewDetails: function(type, id) {
                // Check if user is a nurse
                const isNurse = JSON.parse(`@json($isNurse)`);
                
                if (isNurse) {
                    toast.info('Nurses can only access health-related notifications');
                    return;
                }
                
                let url = '';
                switch (type) {
                    case 'blotter_report':
                        url = '/admin/blotter-reports';
                        break;
                    case 'document_request':
                        url = '/admin/document-requests';
                        break;
                    case 'account_request':
                        url = '/admin/new-account-requests';
                        break;
                    case 'community_complaint':
                        url = '/admin/community-concerns';
                        break;
                    default:
                        toast.error('Unknown notification type');
                        return;
                }

                // Mark as read before redirecting
                fetch(`/admin/notifications/mark-as-read/${type}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    // Optionally update UI here
                    window.location.href = url;
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    // Still redirect even if marking as read fails
                    window.location.href = url;
                });
            },

            // Get time ago string
            getTimeAgo: function(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);

                if (diffInSeconds < 60) {
                    return 'Just now';
                } else if (diffInSeconds < 3600) {
                    const minutes = Math.floor(diffInSeconds / 60);
                    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
                } else if (diffInSeconds < 86400) {
                    const hours = Math.floor(diffInSeconds / 3600);
                    return `${hours} hour${hours > 1 ? 's' : ''} ago`;
                } else {
                    const days = Math.floor(diffInSeconds / 86400);
                    return `${days} day${days > 1 ? 's' : ''} ago`;
                }
            },

            // Start polling for new notifications
            startPolling: function() {
                // Poll every 30 seconds
                setInterval(() => {
                    this.loadNotifications();
                }, 30000);
            },

            // Bind event listeners
            bindEvents: function() {
                // Bind mark all as read button
                window.markAllAsRead = () => {
                    this.markAllAsRead();
                };
            },

                    // Check current page and mark relevant notifications as read
        checkCurrentPageAndMarkNotifications: function() {
            // Check if user is a nurse
            const isNurse = JSON.parse(`@json($isNurse)`);
            
            if (isNurse) {
                return; // Nurses don't have access to barangay-related notifications
            }
            
            const currentPath = window.location.pathname;
            let notificationType = null;
            
            // Map URL patterns to notification types
            if (currentPath.includes('/document-requests')) {
                notificationType = 'document_request';
            } else if (currentPath.includes('/blotter-reports')) {
                notificationType = 'blotter_report';
            } else if (currentPath.includes('/new-account-requests')) {
                notificationType = 'account_request';
            } else if (currentPath.includes('/community-concerns')) {
                notificationType = 'community_complaint';
            }
            
            // If we're on a relevant page, mark notifications as read
            if (notificationType) {
                this.markNotificationsAsReadByType(notificationType);
            }
        },

            // Mark all notifications of a specific type as read
            markNotificationsAsReadByType: function(type) {
                // Check if user is a nurse
                const isNurse = JSON.parse(`@json($isNurse)`);
                
                if (isNurse) {
                    return; // Nurses don't have access to barangay-related notifications
                }
                
                fetch(`/admin/notifications/mark-as-read-by-type/${type}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Automatically marked notifications as read for:', type);
                        // Reload notifications to update the count
                        this.loadNotifications();
                    }
                })
                .catch(error => {
                    console.error('Error marking notifications as read by type:', error);
                });
            }
        };

        // Initialize notification system when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Skip initializing notifications for nurses
            const isNurse = JSON.parse(`@json($isNurse)`);
            if (isNurse) return;

            try {
                notificationSystem.init();
            } catch (error) {
                console.error('Failed to initialize notification system:', error);
                // Fallback: hide notification badge if system fails
                const badge = document.getElementById('notification-count-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        });

        // Enhanced dropdown management
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent dropdown from closing when clicking inside
            const userDropdown = document.querySelector('[x-data*="open: false"]');
            if (userDropdown) {
                const dropdownMenu = userDropdown.querySelector('[role="menu"]');
                if (dropdownMenu) {
                    dropdownMenu.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            }

            // Prevent notification dropdown from closing when clicking inside
            const notificationDropdowns = document.querySelectorAll('.notification-dropdown');
            notificationDropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const dropdowns = document.querySelectorAll('[x-data*="open: false"]');
                    dropdowns.forEach(dropdown => {
                        const alpineData = dropdown._x_dataStack?.[0];
                        if (alpineData && typeof alpineData.open !== 'undefined') {
                            alpineData.open = false;
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</html>