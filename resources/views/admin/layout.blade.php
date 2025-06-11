<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin Page')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans">
    <header class="bg-white text-gray-900 flex items-center justify-between p-4 shadow-md">
        <div class="flex items-center space-x-4">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-900 focus:outline-none mr-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="text-2xl font-bold text-green-600">Barangay Logo</div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="relative cursor-pointer" title="Notifications" x-data="{ open: false }" @click="open = !open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-0 right-0 bg-red-600 text-white rounded-full px-2 text-xs">3</span>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white text-black rounded shadow-lg z-10 p-4">
                    <p class="font-semibold mb-2">Notifications</p>
                    <ul class="list-disc list-inside text-sm">
                        <li>New blotter report submitted</li>
                        <li>Account request approved</li>
                        <li>Project accomplished updated</li>
                    </ul>
                </div>
            </div>
            <div class="relative" x-data="{ open: false }">
<button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
    <i class="fas fa-user text-gray-900"></i>
    <span class="font-semibold">Admin Name</span>
</button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white text-black rounded shadow-lg transition-opacity z-10 p-4" style="display: none;">
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 hover:bg-gray-200 rounded">Profile</a>
                    <div class="border-t border-gray-200 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200 rounded">Logout</button>
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
         style="display: none;">
    </div>

    <div class="flex flex-grow overflow-x-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:block min-w-[17rem] text-gray-900 flex flex-col pt-5 px-1">
            <nav class="flex flex-col space-y-6 overflow-y-auto max-h-[calc(100vh-4rem)] px-2">
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
<i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.users') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
<i class="fas fa-users fa-fw mr-3 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Active Users</span>
                            </a>
                        </li>
                    </ul>
                </section>
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Reports & Requests</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.blotter-reports') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.blotter-reports') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('admin.blotter-reports') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Blotter Reports</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.document-requests') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Document Requests</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.new-account-requests') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.new-account-requests') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Account Requests</span>
                            </a>
                        </li>
                    </ul>
                </section>
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Projects</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.accomplished-projects') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.accomplished-projects') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-check-circle fa-fw mr-3 {{ request()->routeIs('admin.accomplished-projects') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Accomplished Projects</span>
                            </a>
                        </li>
                    </ul>
                </section>
            </nav>
        </aside>

        <!-- Mobile Sidebar -->
        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               @click.stop
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-gray-900 flex flex-col pt-5 px-1 md:hidden"
               style="display: none;">
            <nav class="flex flex-col space-y-6 overflow-y-auto max-h-[calc(100vh-4rem)] px-2">
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Main</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
<i class="fas fa-th-large fa-fw mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.users') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
<i class="fas fa-users fa-fw mr-3 {{ request()->routeIs('admin.users') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Active Users</span>
                            </a>
                        </li>
                    </ul>
                </section>
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Reports & Requests</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.blotter-reports') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.blotter-reports') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-file-alt fa-fw mr-3 {{ request()->routeIs('admin.blotter-reports') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Blotter Reports</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.document-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.document-requests') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-file-signature fa-fw mr-3 {{ request()->routeIs('admin.document-requests') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Document Requests</span>
                            </a>
                        </li>
                        <li>
<a href="{{ route('admin.new-account-requests') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.new-account-requests') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-user-clock fa-fw mr-3 {{ request()->routeIs('admin.new-account-requests') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Account Requests</span>
                            </a>
                        </li>
                    </ul>
                </section>
                <section class="mb-6">
                    <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Projects</h3>
                    <ul class="flex flex-col space-y-2">
                        <li>
<a href="{{ route('admin.accomplished-projects') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.accomplished-projects') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                <i class="fas fa-check-circle fa-fw mr-3 {{ request()->routeIs('admin.accomplished-projects') ? 'text-white' : 'text-green-600' }}"></i>
                                <span>Accomplished Projects</span>
                            </a>
                        </li>
                    </ul>
                </section>
            </nav>
        </aside>

        <main class="flex-grow p-6">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
