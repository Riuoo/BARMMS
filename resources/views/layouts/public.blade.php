<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BARMMS - Lower Malinao Barangay System')</title>
    <script>
        (function(){
            try {
                var path = window.location && window.location.pathname ? window.location.pathname : 'root';
                var key = 'skeletonSeen:' + path;
                if (sessionStorage.getItem(key) === '1') {
                    document.documentElement.classList.add('skeleton-hide');
                }
            } catch(e) {}
        })();
    </script>
    <style>
        .skeleton-hide [data-skeleton],
        .skeleton-hide [id$="Skeleton"] { display: none !important; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-10 w-auto" />
                        <h1 class="text-xl font-bold text-green-600">Lower Malinao System</h1>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-8 w-auto mr-3" />
                    <h3 class="text-lg font-bold">Lower Malinao System</h3>
                </div>
                <p class="text-gray-400 mb-4">Empowering our community through digital innovation and better service delivery.</p>
                <div class="border-t border-gray-800 pt-4">
                    <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} Lower Malinao Barangay System. All rights reserved.</p>
                    <div class="mt-2 space-x-4">
                        <a href="{{ route('public.privacy') }}" class="text-gray-400 hover:text-white transition duration-300 text-sm">Privacy Policy</a>
                        <a href="{{ route('public.terms') }}" class="text-gray-400 hover:text-white transition duration-300 text-sm">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        (function() {
            try {
                var path = window.location && window.location.pathname ? window.location.pathname : 'root';
                var key = 'skeletonSeen:' + path;
                var skeletons = document.querySelectorAll('[data-skeleton], [id$="Skeleton"]');
                if (!skeletons || skeletons.length === 0) return;
                var seen = sessionStorage.getItem(key) === '1';
                if (seen) {
                    skeletons.forEach(function(el) { el.style.display = 'none'; });
                    // Instant reveal for primary content containers if present
                    try {
                        var contentNodes = document.querySelectorAll('[id$="Content"], [data-content]');
                        contentNodes.forEach(function(n){ n.style.display = ''; });
                    } catch(e) {}
                } else {
                    sessionStorage.setItem(key, '1');
                }
            } catch (e) {}
            // expose helper to clear flags
            window.clearSkeletonFlags = function() {
                try {
                    var keysToRemove = [];
                    for (var i = 0; i < sessionStorage.length; i++) {
                        var k = sessionStorage.key(i);
                        if (k && k.indexOf('skeletonSeen:') === 0) keysToRemove.push(k);
                    }
                    keysToRemove.forEach(function(k){ sessionStorage.removeItem(k); });
                } catch(e) {}
            };
        })();
    </script>
</body>
</html>
