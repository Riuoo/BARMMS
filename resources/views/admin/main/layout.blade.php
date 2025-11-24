<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <script>
        (function(){
            // Set theme immediately before any rendering
            try {
                var savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch(e) {}
        })();
    </script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin Page')</title>
    <script>
        (function(){
            try {
                var path = window.location && window.location.pathname ? window.location.pathname : 'root';
                var key = 'skeletonSeen:' + path;
                // If we've already visited this page once, hide skeletons immediately
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
    <style>
        /* Improve skeleton visibility in dark mode */
        [data-theme="dark"] [data-skeleton] .bg-gray-100 { background-color: #374151 !important; }
        [data-theme="dark"] [data-skeleton] .bg-gray-200 { background-color: #4b5563 !important; }
        [data-theme="dark"] [data-skeleton] .bg-gray-300 { background-color: #6b7280 !important; }
        [data-theme="dark"] [data-skeleton] .border-gray-100,
        [data-theme="dark"] [data-skeleton] .border-gray-200 { border-color: #475569 !important; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css'])
    @notifyCss
    <style type="text/css">
        /* Dark Mode CSS Variables */
        :root {
          --bg-primary: #f9fafb;
          --bg-secondary: #ffffff;
          --bg-sidebar: #ffffff;
          --text-primary: #111827;
          --text-secondary: #6b7280;
          --text-muted: #9ca3af;
          --border-color: #e5e7eb;
          --shadow-color: rgba(0, 0, 0, 0.1);
          --hover-bg: #f3f4f6;
          --active-bg: #10b981;
          --active-text: #ffffff;
        }

        [data-theme="dark"] {
          --bg-primary: #111827;
          --bg-secondary: #1f2937;
          --bg-sidebar: #1f2937;
          --text-primary: #f9fafb;
          --text-secondary: #d1d5db;
          --text-muted: #9ca3af;
          --border-color: #374151;
          --shadow-color: rgba(0, 0, 0, 0.3);
          --hover-bg: #374151;
          --active-bg: #10b981;
          --active-text: #ffffff;
        }

        /* Dark Mode Base Styles */
        [data-theme="dark"] {
          background-color: var(--bg-primary);
          color: var(--text-primary);
        }

        [data-theme="dark"] .bg-white {
          background-color: var(--bg-secondary) !important;
        }

        [data-theme="dark"] .bg-gray-100 {
          background-color: var(--bg-primary) !important;
        }

        [data-theme="dark"] .text-gray-900 {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-gray-700 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-gray-500 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-gray-400 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .border-gray-200 {
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .border-gray-300 {
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .hover\:bg-gray-300:hover {
          background-color: var(--hover-bg) !important;
        }

        [data-theme="dark"] .hover\:bg-gray-50:hover {
          background-color: var(--hover-bg) !important;
        }

        [data-theme="dark"] .hover\:bg-green-50:hover {
          background-color: rgba(16, 185, 129, 0.1) !important;
        }

        [data-theme="dark"] .hover\:bg-red-50:hover {
          background-color: rgba(239, 68, 68, 0.1) !important;
        }

        [data-theme="dark"] .shadow-md {
          box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color) !important;
        }

        [data-theme="dark"] .shadow-xl {
          box-shadow: 0 20px 25px -5px var(--shadow-color), 0 10px 10px -5px var(--shadow-color) !important;
        }

        /* Dark mode transition */
        * {
          transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        /* Additional dark mode styles for admin dashboard */
        [data-theme="dark"] .bg-gradient-to-br {
          background: linear-gradient(to bottom right, var(--bg-secondary), var(--bg-primary)) !important;
        }

        [data-theme="dark"] .bg-white.rounded-xl {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .text-gray-900 {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-gray-600 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .border-gray-100 {
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .bg-green-50 {
          background-color: rgba(16, 185, 129, 0.1) !important;
        }

        [data-theme="dark"] .text-green-800 {
          color: #10b981 !important;
        }

        [data-theme="dark"] .border-green-200 {
          border-color: rgba(16, 185, 129, 0.3) !important;
        }

        /* Additional dark mode fixes for text visibility */
        [data-theme="dark"] .text-gray-800 {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-gray-600 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-gray-500 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-gray-400 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-gray-300 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-gray-200 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-gray-100 {
          color: var(--text-primary) !important;
        }

        /* Fix date text visibility in dark mode */
        [data-theme="dark"] .text-sm.text-gray-900 {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-xs.text-gray-500 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-sm.text-gray-700 {
          color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .text-sm.text-gray-500 {
          color: var(--text-muted) !important;
        }

        [data-theme="dark"] .text-xs.text-gray-900 {
          color: var(--text-primary) !important;
        }

        /* Dark mode for table elements */
        [data-theme="dark"] table th {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] table td {
          background-color: var(--bg-secondary) !important;
          color: var(--text-primary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] table tr:nth-child(even) td {
          background-color: var(--bg-primary) !important;
        }

        [data-theme="dark"] table tr:hover td {
          background-color: var(--hover-bg) !important;
        }

        /* Dark mode for form elements */
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="password"],
        [data-theme="dark"] input[type="number"],
        [data-theme="dark"] input[type="search"],
        [data-theme="dark"] textarea,
        [data-theme="dark"] select {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder {
          color: var(--text-muted) !important;
        }

        /* Fix datetime input text visibility */
        [data-theme="dark"] input[type="date"],
        [data-theme="dark"] input[type="datetime-local"],
        [data-theme="dark"] input[type="time"] {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator,
        [data-theme="dark"] input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        [data-theme="dark"] input[type="time"]::-webkit-calendar-picker-indicator {
          filter: invert(1);
        }

        /* Dark mode for buttons - only fix gray/black/white buttons */
        [data-theme="dark"] .btn.text-gray-900,
        [data-theme="dark"] .btn.text-gray-800,
        [data-theme="dark"] .btn.text-gray-700,
        [data-theme="dark"] .btn.text-gray-600,
        [data-theme="dark"] .btn.text-gray-500,
        [data-theme="dark"] .btn.text-gray-400,
        [data-theme="dark"] .btn.text-gray-300,
        [data-theme="dark"] .btn.text-gray-200,
        [data-theme="dark"] .btn.text-gray-100,
        [data-theme="dark"] .btn.text-black,
        [data-theme="dark"] .btn.text-white,
        [data-theme="dark"] button.text-gray-900,
        [data-theme="dark"] button.text-gray-800,
        [data-theme="dark"] button.text-gray-700,
        [data-theme="dark"] button.text-gray-600,
        [data-theme="dark"] button.text-gray-500,
        [data-theme="dark"] button.text-gray-400,
        [data-theme="dark"] button.text-gray-300,
        [data-theme="dark"] button.text-gray-200,
        [data-theme="dark"] button.text-gray-100,
        [data-theme="dark"] button.text-black,
        [data-theme="dark"] button.text-white {
          color: var(--text-primary) !important;
        }

        /* Keep original button colors - don't override them */
        /* [data-theme="dark"] .btn-primary {
          background-color: var(--active-bg) !important;
          border-color: var(--active-bg) !important;
          color: var(--active-text) !important;
        }

        [data-theme="dark"] .btn-secondary {
          background-color: var(--bg-primary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        } */

        /* Dark mode for badges and pills - only fix gray/black/white badges */
        [data-theme="dark"] .badge.text-gray-900,
        [data-theme="dark"] .badge.text-gray-800,
        [data-theme="dark"] .badge.text-gray-700,
        [data-theme="dark"] .badge.text-gray-600,
        [data-theme="dark"] .badge.text-gray-500,
        [data-theme="dark"] .badge.text-gray-400,
        [data-theme="dark"] .badge.text-gray-300,
        [data-theme="dark"] .badge.text-gray-200,
        [data-theme="dark"] .badge.text-gray-100,
        [data-theme="dark"] .badge.text-black,
        [data-theme="dark"] .badge.text-white,
        [data-theme="dark"] .pill.text-gray-900,
        [data-theme="dark"] .pill.text-gray-800,
        [data-theme="dark"] .pill.text-gray-700,
        [data-theme="dark"] .pill.text-gray-600,
        [data-theme="dark"] .pill.text-gray-500,
        [data-theme="dark"] .pill.text-gray-400,
        [data-theme="dark"] .pill.text-gray-300,
        [data-theme="dark"] .pill.text-gray-200,
        [data-theme="dark"] .pill.text-gray-100,
        [data-theme="dark"] .pill.text-black,
        [data-theme="dark"] .pill.text-white,
        [data-theme="dark"] .inline-flex.text-gray-900,
        [data-theme="dark"] .inline-flex.text-gray-800,
        [data-theme="dark"] .inline-flex.text-gray-700,
        [data-theme="dark"] .inline-flex.text-gray-600,
        [data-theme="dark"] .inline-flex.text-gray-500,
        [data-theme="dark"] .inline-flex.text-gray-400,
        [data-theme="dark"] .inline-flex.text-gray-300,
        [data-theme="dark"] .inline-flex.text-gray-200,
        [data-theme="dark"] .inline-flex.text-gray-100,
        [data-theme="dark"] .inline-flex.text-black,
        [data-theme="dark"] .inline-flex.text-white {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
          border-color: var(--border-color) !important;
        }

        /* Keep original colored badge backgrounds - don't override them */

        [data-theme="dark"] .bg-gray-100 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-200 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-300 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-400 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-500 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-600 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-700 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-800 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .bg-gray-900 {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
        }

        /* Dark mode for status badges */
        [data-theme="dark"] .status-active,
        [data-theme="dark"] .status-pending,
        [data-theme="dark"] .status-inactive {
          color: var(--text-primary) !important;
        }

        /* Dark mode for icons - only fix gray/black/white icons */
        [data-theme="dark"] .fas.text-gray-900,
        [data-theme="dark"] .fas.text-gray-800,
        [data-theme="dark"] .fas.text-gray-700,
        [data-theme="dark"] .fas.text-gray-600,
        [data-theme="dark"] .fas.text-gray-500,
        [data-theme="dark"] .fas.text-gray-400,
        [data-theme="dark"] .fas.text-gray-300,
        [data-theme="dark"] .fas.text-gray-200,
        [data-theme="dark"] .fas.text-gray-100,
        [data-theme="dark"] .fas.text-black,
        [data-theme="dark"] .fas.text-white {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .far.text-gray-900,
        [data-theme="dark"] .far.text-gray-800,
        [data-theme="dark"] .far.text-gray-700,
        [data-theme="dark"] .far.text-gray-600,
        [data-theme="dark"] .far.text-gray-500,
        [data-theme="dark"] .far.text-gray-400,
        [data-theme="dark"] .far.text-gray-300,
        [data-theme="dark"] .far.text-gray-200,
        [data-theme="dark"] .far.text-gray-100,
        [data-theme="dark"] .far.text-black,
        [data-theme="dark"] .far.text-white {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .fab.text-gray-900,
        [data-theme="dark"] .fab.text-gray-800,
        [data-theme="dark"] .fab.text-gray-700,
        [data-theme="dark"] .fab.text-gray-600,
        [data-theme="dark"] .fab.text-gray-500,
        [data-theme="dark"] .fab.text-gray-400,
        [data-theme="dark"] .fab.text-gray-300,
        [data-theme="dark"] .fab.text-gray-200,
        [data-theme="dark"] .fab.text-gray-100,
        [data-theme="dark"] .fab.text-black,
        [data-theme="dark"] .fab.text-white {
          color: var(--text-primary) !important;
        }

        /* Keep original colors for colored text - don't override them */
        /* [data-theme="dark"] .text-green-600 {
          color: #10b981 !important;
        }

        [data-theme="dark"] .text-blue-600 {
          color: #3b82f6 !important;
        }

        [data-theme="dark"] .text-red-600 {
          color: #ef4444 !important;
        }

        [data-theme="dark"] .text-yellow-600 {
          color: #f59e0b !important;
        }

        [data-theme="dark"] .text-purple-600 {
          color: #8b5cf6 !important;
        } */

        /* Dark mode for links - only fix gray/black/white links */
        [data-theme="dark"] a.text-gray-900,
        [data-theme="dark"] a.text-gray-800,
        [data-theme="dark"] a.text-gray-700,
        [data-theme="dark"] a.text-gray-600,
        [data-theme="dark"] a.text-gray-500,
        [data-theme="dark"] a.text-gray-400,
        [data-theme="dark"] a.text-gray-300,
        [data-theme="dark"] a.text-gray-200,
        [data-theme="dark"] a.text-gray-100,
        [data-theme="dark"] a.text-black,
        [data-theme="dark"] a.text-white {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] a.text-gray-900:hover,
        [data-theme="dark"] a.text-gray-800:hover,
        [data-theme="dark"] a.text-gray-700:hover,
        [data-theme="dark"] a.text-gray-600:hover,
        [data-theme="dark"] a.text-gray-500:hover,
        [data-theme="dark"] a.text-gray-400:hover,
        [data-theme="dark"] a.text-gray-300:hover,
        [data-theme="dark"] a.text-gray-200:hover,
        [data-theme="dark"] a.text-gray-100:hover,
        [data-theme="dark"] a.text-black:hover,
        [data-theme="dark"] a.text-white:hover {
          color: var(--active-bg) !important;
        }

        /* Dark mode for dropdowns */
        [data-theme="dark"] .dropdown-menu {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dropdown-item {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .dropdown-item:hover {
          background-color: var(--hover-bg) !important;
          color: var(--text-primary) !important;
        }

        /* Dark mode for pagination */
        [data-theme="dark"] .pagination .page-link {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .pagination .page-link:hover {
          background-color: var(--hover-bg) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .pagination .page-item.active .page-link {
          background-color: var(--active-bg) !important;
          border-color: var(--active-bg) !important;
          color: var(--active-text) !important;
        }

        /* Additional specific fixes for common visibility issues */
        [data-theme="dark"] .card,
        [data-theme="dark"] .card-body {
          background-color: var(--bg-secondary) !important;
          color: var(--text-primary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .card-header {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .card-title {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .card-text {
          color: var(--text-secondary) !important;
        }

        /* Fix for role pills and badges - only fix gray/black/white badges */
        [data-theme="dark"] .badge-pill.text-gray-900,
        [data-theme="dark"] .badge-pill.text-gray-800,
        [data-theme="dark"] .badge-pill.text-gray-700,
        [data-theme="dark"] .badge-pill.text-gray-600,
        [data-theme="dark"] .badge-pill.text-gray-500,
        [data-theme="dark"] .badge-pill.text-gray-400,
        [data-theme="dark"] .badge-pill.text-gray-300,
        [data-theme="dark"] .badge-pill.text-gray-200,
        [data-theme="dark"] .badge-pill.text-gray-100,
        [data-theme="dark"] .badge-pill.text-black,
        [data-theme="dark"] .badge-pill.text-white,
        [data-theme="dark"] .badge-secondary {
          background-color: var(--bg-primary) !important;
          color: var(--text-primary) !important;
          border: 1px solid var(--border-color) !important;
        }

        /* Keep original colored badges - don't override them */
        /* [data-theme="dark"] .badge-success {
          background-color: var(--active-bg) !important;
          color: var(--active-text) !important;
        }

        [data-theme="dark"] .badge-warning {
          background-color: #f59e0b !important;
          color: #000000 !important;
        }

        [data-theme="dark"] .badge-danger {
          background-color: #ef4444 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .badge-info {
          background-color: #3b82f6 !important;
          color: #ffffff !important;
        } */

        /* Fix for action buttons in tables - only fix gray/black/white buttons */
        [data-theme="dark"] .btn-sm.text-gray-900,
        [data-theme="dark"] .btn-sm.text-gray-800,
        [data-theme="dark"] .btn-sm.text-gray-700,
        [data-theme="dark"] .btn-sm.text-gray-600,
        [data-theme="dark"] .btn-sm.text-gray-500,
        [data-theme="dark"] .btn-sm.text-gray-400,
        [data-theme="dark"] .btn-sm.text-gray-300,
        [data-theme="dark"] .btn-sm.text-gray-200,
        [data-theme="dark"] .btn-sm.text-gray-100,
        [data-theme="dark"] .btn-sm.text-black,
        [data-theme="dark"] .btn-sm.text-white {
          background-color: var(--bg-primary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .btn-sm.text-gray-900:hover,
        [data-theme="dark"] .btn-sm.text-gray-800:hover,
        [data-theme="dark"] .btn-sm.text-gray-700:hover,
        [data-theme="dark"] .btn-sm.text-gray-600:hover,
        [data-theme="dark"] .btn-sm.text-gray-500:hover,
        [data-theme="dark"] .btn-sm.text-gray-400:hover,
        [data-theme="dark"] .btn-sm.text-gray-300:hover,
        [data-theme="dark"] .btn-sm.text-gray-200:hover,
        [data-theme="dark"] .btn-sm.text-gray-100:hover,
        [data-theme="dark"] .btn-sm.text-black:hover,
        [data-theme="dark"] .btn-sm.text-white:hover {
          background-color: var(--hover-bg) !important;
          color: var(--text-primary) !important;
        }

        /* Keep original colored button backgrounds - don't override them */

        /* Preserve specific button colors from light mode */
        /* Green buttons - Create New Report, Filter, etc. */
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-green-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-green-700 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-green-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-green-700:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        /* Blue buttons - Analysis Dashboard, etc. */
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-blue-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-blue-700 {
          background-color: #2563eb !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-blue-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-blue-700:hover {
          background-color: #1d4ed8 !important;
          color: #ffffff !important;
        }

        /* White buttons with gray text - Reset, Show All, etc. */
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.border.border-gray-300.text-sm.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50 {
          background-color: #ffffff !important;
          color: #374151 !important;
          border-color: #d1d5db !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.border.border-gray-300.text-sm.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

        /* Small white buttons */
        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50 {
          background-color: #ffffff !important;
          color: #374151 !important;
          border-color: #d1d5db !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

        /* FAB (Floating Action Button) */
        [data-theme="dark"] .bg-green-600.hover\:bg-green-700.text-white.rounded-full.p-4 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .bg-green-600.hover\:bg-green-700.text-white.rounded-full.p-4:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        /* FAB Menu items */
        [data-theme="dark"] .flex.items-center.bg-white.rounded-lg.shadow-lg.p-3.hover\:shadow-xl {
          background-color: #ffffff !important;
          color: #374151 !important;
        }

        [data-theme="dark"] .flex.items-center.bg-white.rounded-lg.shadow-lg.p-3.hover\:shadow-xl:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

        /* Preserve icon colors in FAB menu */
        [data-theme="dark"] .fas.fa-user-plus.text-blue-600 {
          color: #2563eb !important;
        }

        [data-theme="dark"] .fas.fa-home.text-green-600 {
          color: #059669 !important;
        }

        [data-theme="dark"] .fas.fa-file-alt.text-purple-600 {
          color: #9333ea !important;
        }

        /* Additional filter button styles */
        /* Blue filter button (FAQs) */
        [data-theme="dark"] .bg-blue-600.text-white.px-4.py-2.rounded.hover\:bg-blue-700 {
          background-color: #2563eb !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .bg-blue-600.text-white.px-4.py-2.rounded.hover\:bg-blue-700:hover {
          background-color: #1d4ed8 !important;
          color: #ffffff !important;
        }

        /* Clear Filters button */
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.border.border-gray-300.text-sm.font-medium.rounded-lg.text-gray-700.bg-white.hover\:bg-gray-50 {
          background-color: #ffffff !important;
          color: #374151 !important;
          border-color: #d1d5db !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.border.border-gray-300.text-sm.font-medium.rounded-lg.text-gray-700.bg-white.hover\:bg-gray-50:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

            /* Preserve specific colored buttons used in tables */
        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-teal-600.hover\:bg-teal-700 {
          background-color: #0d9488 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-teal-600.hover\:bg-teal-700:hover {
          background-color: #0f766e !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-green-600.hover\:bg-green-700 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-green-600.hover\:bg-green-700:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-teal-600.hover\:bg-teal-700 {
          background-color: #0d9488 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-teal-600.hover\:bg-teal-700:hover {
          background-color: #0f766e !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        /* Preserve other colored buttons */
        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-blue-600.hover\:bg-blue-700 {
          background-color: #2563eb !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-blue-600.hover\:bg-blue-700:hover {
          background-color: #1d4ed8 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-red-600.hover\:bg-red-700 {
          background-color: #dc2626 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-red-600.hover\:bg-red-700:hover {
          background-color: #b91c1c !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-yellow-600.hover\:bg-yellow-700 {
          background-color: #d97706 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-yellow-600.hover\:bg-yellow-700:hover {
          background-color: #b45309 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-purple-600.hover\:bg-purple-700 {
          background-color: #9333ea !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-purple-600.hover\:bg-purple-700:hover {
          background-color: #7c3aed !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-indigo-600.hover\:bg-indigo-700 {
          background-color: #4f46e5 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.justify-center.px-2.py-1.border.border-transparent.text-xs.font-medium.rounded.text-white.bg-indigo-600.hover\:bg-indigo-700:hover {
          background-color: #4338ca !important;
          color: #ffffff !important;
        }

        /* Preserve specific button styles used in the application */
        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50 {
          background-color: #ffffff !important;
          color: #374151 !important;
          border-color: #d1d5db !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50 {
          background-color: #ffffff !important;
          color: #374151 !important;
          border-color: #d1d5db !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.text-gray-700.bg-white.hover\:bg-gray-50:hover {
          background-color: #f9fafb !important;
          color: #374151 !important;
        }

        /* Preserve green buttons */
        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700 {
          background-color: #059669 !important;
          color: #ffffff !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-3.py-1\.5.border.border-transparent.text-xs.font-medium.rounded-md.text-white.bg-green-600.hover\:bg-green-700:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        /* Preserve toggle buttons */
        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.bg-green-100.text-green-800.hover\:bg-green-200 {
          background-color: #dcfce7 !important;
          color: #166534 !important;
          border-color: #bbf7d0 !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.bg-green-100.text-green-800.hover\:bg-green-200:hover {
          background-color: #bbf7d0 !important;
          color: #166534 !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.bg-red-100.text-red-800.hover\:bg-red-200 {
          background-color: #fee2e2 !important;
          color: #991b1b !important;
          border-color: #fecaca !important;
        }

        [data-theme="dark"] .inline-flex.items-center.px-2.py-1\.5.border.border-gray-300.text-xs.font-medium.rounded-md.bg-red-100.text-red-800.hover\:bg-red-200:hover {
          background-color: #fecaca !important;
          color: #991b1b !important;
        }

        /* Fix for search and filter elements */
        [data-theme="dark"] .form-control {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .form-control:focus {
          background-color: var(--bg-secondary) !important;
          border-color: var(--active-bg) !important;
          color: var(--text-primary) !important;
          box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25) !important;
        }

        /* Fix for modal elements */
        [data-theme="dark"] .modal-content {
          background-color: var(--bg-secondary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .modal-header {
          background-color: var(--bg-primary) !important;
          border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .modal-body {
          background-color: var(--bg-secondary) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .modal-footer {
          background-color: var(--bg-primary) !important;
          border-color: var(--border-color) !important;
        }

        /* Fix for alert elements */
        [data-theme="dark"] .alert {
          background-color: var(--bg-primary) !important;
          border-color: var(--border-color) !important;
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] .alert-success {
          background-color: rgba(16, 185, 129, 0.1) !important;
          border-color: var(--active-bg) !important;
          color: var(--active-bg) !important;
        }

        [data-theme="dark"] .alert-danger {
          background-color: rgba(239, 68, 68, 0.1) !important;
          border-color: #ef4444 !important;
          color: #ef4444 !important;
        }

        [data-theme="dark"] .alert-warning {
          background-color: rgba(245, 158, 11, 0.1) !important;
          border-color: #f59e0b !important;
          color: #f59e0b !important;
        }

        [data-theme="dark"] .alert-info {
          background-color: rgba(59, 130, 246, 0.1) !important;
          border-color: #3b82f6 !important;
          color: #3b82f6 !important;
        }

        .notify {
            z-index: 1001 !important;
        }

        /* Fix top navigation buttons visibility in dark mode */
        [data-theme="dark"] header .relative.focus\:outline-none.rounded-md.p-1 {
          background-color: var(--bg-secondary) !important;
          border: none !important;
          transition: all 0.2s ease-in-out !important;
        }

        [data-theme="dark"] header .relative.focus\:outline-none.rounded-md.p-1:hover {
          background-color: var(--hover-bg) !important;
          border: none !important;
        }

        [data-theme="dark"] header .flex.items-center.space-x-2.focus\:outline-none.rounded-md.p-1 {
          background-color: var(--bg-secondary) !important;
          border: none !important;
          transition: all 0.2s ease-in-out !important;
        }

        [data-theme="dark"] header .flex.items-center.space-x-2.focus\:outline-none.rounded-md.p-1:hover {
          background-color: var(--hover-bg) !important;
          border: none !important;
        }

        /* Fix top navigation button icons */
        [data-theme="dark"] header .fas.fa-bell.text-gray-900 {
          color: var(--text-primary) !important;
        }

        [data-theme="dark"] header .fas.fa-user.text-gray-900 {
          color: var(--text-primary) !important;
        }

        /* Fix top navigation button text */
        [data-theme="dark"] header .font-semibold {
          color: var(--text-primary) !important;
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

        /* HIGH SPECIFICITY: Force Filter button to be green in dark mode, even if other general button overrides exist */
        [data-theme="dark"] button.bg-green-600,
        [data-theme="dark"] a.bg-green-600,
        [data-theme="dark"] .bg-green-600.text-white,
        [data-theme="dark"] .inline-flex.bg-green-600.text-white,
        [data-theme="dark"] .px-4.py-2.bg-green-600.text-white,
        [data-theme="dark"] .hover\:bg-green-700.bg-green-600.text-white {
          background-color: #059669 !important;
          color: #ffffff !important;
          border: none;
        }
        [data-theme="dark"] button.bg-green-600:hover,
        [data-theme="dark"] a.bg-green-600:hover {
          background-color: #047857 !important;
          color: #ffffff !important;
        }

        /* HIGH SPECIFICITY: Force colored buttons to keep their backgrounds in dark mode */
        
        /* Teal buttons */
        [data-theme="dark"] button.bg-teal-600,
        [data-theme="dark"] a.bg-teal-600,
        [data-theme="dark"] .bg-teal-600.text-white,
        [data-theme="dark"] .inline-flex.bg-teal-600.text-white,
        [data-theme="dark"] .px-4.py-2.bg-teal-600.text-white,
        [data-theme="dark"] .hover\:bg-teal-700.bg-teal-600.text-white {
          background-color: #0d9488 !important;
          color: #ffffff !important;
          border: none;
        }
        [data-theme="dark"] button.bg-teal-600:hover,
        [data-theme="dark"] a.bg-teal-600:hover,
        [data-theme="dark"] .hover\:bg-teal-700.bg-teal-600.text-white:hover {
          background-color: #0f766e !important;
          color: #ffffff !important;
        }

        /* Red buttons */
        [data-theme="dark"] .inline-flex.items-center.px-6.py-2.border.border-transparent.text-sm.font-medium.rounded-lg.text-white.bg-red-600.hover\:bg-red-700 {
          background-color: #dc2626 !important;
          color: #ffffff !important;
        }
        [data-theme="dark"] .inline-flex.items-center.px-6.py-2.border.border-transparent.text-sm.font-medium.rounded-lg.text-white.bg-red-600.hover\:bg-red-700:hover {
          background-color: #b91c1c !important;
          color: #ffffff !important;
        }

        /* Blue buttons */
        [data-theme="dark"] .inline-flex.items-center.px-6.py-2.border.border-transparent.text-sm.font-medium.rounded-lg.text-white.bg-blue-600.hover\:bg-blue-700 {
          background-color: #2563eb !important;
          color: #ffffff !important;
        }
        [data-theme="dark"] .inline-flex.items-center.px-6.py-2.border.border-transparent.text-sm.font-medium.rounded-lg.text-white.bg-blue-600.hover\:bg-blue-700:hover {
          background-color: #1d4ed8 !important;
          color: #ffffff !important;
        }

        /* Yellow buttons */
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-yellow-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-yellow-700 {
          background-color: #d97706 !important;
          color: #ffffff !important;
        }
        [data-theme="dark"] .inline-flex.items-center.px-4.py-2.bg-yellow-600.text-white.text-sm.font-medium.rounded-lg.hover\:bg-yellow-700:hover {
          background-color: #b45309 !important;
          color: #ffffff !important;
        }

        /* Dashboard card icon backgrounds - keep colored in dark mode */
        /* Single color backgrounds */
        [data-theme="dark"] .bg-green-100,
        [data-theme="dark"] .rounded-full.bg-green-100   { background-color: #d1fae5 !important; color: #059669 !important; }
        [data-theme="dark"] .bg-green-200,
        [data-theme="dark"] .rounded-full.bg-green-200   { background-color: #a7f3d0 !important; color: #047857 !important; }
        [data-theme="dark"] .bg-blue-100,
        [data-theme="dark"] .rounded-full.bg-blue-100    { background-color: #dbeafe !important; color: #2563eb !important; }
        [data-theme="dark"] .bg-blue-200,
        [data-theme="dark"] .rounded-full.bg-blue-200    { background-color: #bfdbfe !important; color: #1d4ed8 !important; }
        [data-theme="dark"] .bg-yellow-100,
        [data-theme="dark"] .rounded-full.bg-yellow-100  { background-color: #fef9c3 !important; color: #b45309 !important; }
        [data-theme="dark"] .bg-yellow-200,
        [data-theme="dark"] .rounded-full.bg-yellow-200  { background-color: #fef08a !important; color: #b45309 !important; }
        [data-theme="dark"] .bg-red-100,
        [data-theme="dark"] .rounded-full.bg-red-100     { background-color: #fee2e2 !important; color: #dc2626 !important; }
        [data-theme="dark"] .bg-red-200,
        [data-theme="dark"] .rounded-full.bg-red-200     { background-color: #fecaca !important; color: #b91c1c !important; }
        [data-theme="dark"] .bg-purple-100,
        [data-theme="dark"] .rounded-full.bg-purple-100  { background-color: #ede9fe !important; color: #9333ea !important; }
        [data-theme="dark"] .bg-orange-100,
        [data-theme="dark"] .rounded-full.bg-orange-100  { background-color: #ffedd5 !important; color: #ea580c !important; }
        [data-theme="dark"] .bg-pink-100,
        [data-theme="dark"] .rounded-full.bg-pink-100    { background-color: #fce7f3 !important; color: #db2777 !important; }
        [data-theme="dark"] .bg-teal-100,
        [data-theme="dark"] .rounded-full.bg-teal-100    { background-color: #ccfbf1 !important; color: #0d9488 !important; }
        [data-theme="dark"] .bg-indigo-100,
        [data-theme="dark"] .rounded-full.bg-indigo-100   { background-color: #e0e7ff !important; color: #4f46e5 !important; }
        
        /* Gradient backgrounds for icon circles */
        [data-theme="dark"] .bg-gradient-to-br.from-green-100.to-green-200 { 
          background: linear-gradient(to bottom right, #d1fae5, #a7f3d0) !important; 
          color: #047857 !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-blue-100.to-blue-200 { 
          background: linear-gradient(to bottom right, #dbeafe, #bfdbfe) !important; 
          color: #1d4ed8 !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-purple-100.to-purple-200 { 
          background: linear-gradient(to bottom right, #ede9fe, #ddd6fe) !important; 
          color: #7c3aed !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-orange-100.to-orange-200 { 
          background: linear-gradient(to bottom right, #ffedd5, #fed7aa) !important; 
          color: #ea580c !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-red-100.to-red-200 { 
          background: linear-gradient(to bottom right, #fee2e2, #fecaca) !important; 
          color: #b91c1c !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-yellow-100.to-yellow-200 { 
          background: linear-gradient(to bottom right, #fef9c3, #fef08a) !important; 
          color: #b45309 !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-pink-100.to-pink-200 { 
          background: linear-gradient(to bottom right, #fce7f3, #fbcfe8) !important; 
          color: #db2777 !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-teal-100.to-teal-200 { 
          background: linear-gradient(to bottom right, #ccfbf1, #99f6e4) !important; 
          color: #0f766e !important; 
        }
        [data-theme="dark"] .bg-gradient-to-br.from-indigo-100.to-indigo-200 { 
          background: linear-gradient(to bottom right, #e0e7ff, #c7d2fe) !important; 
          color: #4338ca !important; 
        }
    </style>
    <style>
.darkmode-switch-btn {
  padding: 0;
  border: none;
  outline: none;
  background:#aaa;transition:background 0.2s;width:32px;height:18px;box-shadow:none;cursor:pointer;display:inline-block;}
.darkmode-switch-btn[aria-pressed='true']{background:#22c55e!important;}
.darkmode-switch-btn[aria-pressed='false']{background:#aaa!important;}
.darkmode-switch-btn .darkmode-switch-thumb{transition:transform 0.2s;left:2px;top:2px;width:14px;height:14px;}
.darkmode-switch-btn[aria-pressed='true'] .darkmode-switch-thumb{transform:translateX(14px);}
.darkmode-switch-btn[aria-pressed='false'] .darkmode-switch-thumb{transform:translateX(0);}
.darkmode-switch-label { color: #222; }
[data-theme='dark'] .darkmode-switch-label { color: #fff; }
</style>
<style>
.segment-btn {background:transparent;border:none;color:#ddd;box-shadow:none;height:100%;}
.segment-btn.active {background:#ef6c1d;color:#fff;}
.segment-btn:focus {outline:none;}
</style>
<style>
.menu-icon { font-size: 1rem; display:inline-block; vertical-align:middle; }
</style>
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notifications-url="{{ route('admin.notifications.count') }}" data-notify-timeout="{{ config('notify.timeout', 5000) }}" data-theme="light">
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
                
                const borderColors = {
                    success: 'border-l-green-500',
                    error: 'border-l-red-500',
                    warning: 'border-l-yellow-500',
                    info: 'border-l-blue-500'
                };
                
                const iconColors = {
                    success: 'text-green-500',
                    error: 'text-red-500',
                    warning: 'text-yellow-500',
                    info: 'text-blue-500'
                };
                
                const icons = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-exclamation-circle',
                    warning: 'fas fa-exclamation-triangle',
                    info: 'fas fa-info-circle'
                };
                
                const titles = {
                    success: 'Success',
                    error: 'Error',
                    warning: 'Warning',
                    info: 'Info'
                };
                
                toast.className = `bg-white border-l-4 ${borderColors[type] || borderColors.info} rounded-lg shadow-lg p-4 transform translate-x-full transition-all duration-300 mb-2`;
                toast.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="${icons[type] || icons.info} ${iconColors[type] || iconColors.info} text-lg"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-gray-900">${titles[type] || titles.info}</h3>
                            <p class="text-sm text-gray-600 mt-1">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="this.closest('.bg-white').remove()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
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
                            <button onclick="openMarkAllReadModal()" class="text-gray-600 hover:text-gray-800 text-sm transition duration-200">
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
                        <!-- Desktop profile dropdown theme toggle -->
                        <div class="hidden md:block">
                          <div class="flex items-center px-4 py-3 text-sm gap-3">
                            <i class="fas fa-moon menu-icon text-gray-400"></i>
                            <span class="darkmode-switch-label" style="margin-right:auto;">Dark Mode</span>
                            <button id="admin-desktop-darkmode-switch" type="button"
                                class="relative w-8 h-4.5 rounded-full focus:outline-none darkmode-switch-btn border-none"
                                aria-pressed="false"
                                onclick="toggleGreenDarkModeSwitch('admin-desktop')">
                              <span class="absolute top-0.5 left-0.5 transition-all duration-200 w-3.5 h-3.5 rounded-full bg-white darkmode-switch-thumb shadow-md"></span>
                            </button>
                          </div>
                        </div>
                        
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

                    @if($isNurse || $isAdmin || $isSecretary || $isCaptain || $isCouncilor)
                    <!-- QR Code & Attendance Section -->
                    <section class="mb-6" aria-label="QR Code & Attendance">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">QR Code & Attendance</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.attendance.scanner') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.attendance.scanner*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.attendance.scanner*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-qrcode fa-fw mr-3 {{ request()->routeIs('admin.attendance.scanner*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>QR Scanner</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.attendance.logs') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.attendance.logs*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.attendance.logs*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-list fa-fw mr-3 {{ request()->routeIs('admin.attendance.logs*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Attendance Logs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.events.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.events*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.events*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-calendar-check fa-fw mr-3 {{ request()->routeIs('admin.events*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Events</span>
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
                    
                    @if(!$isNurse)
                    <!-- Settings / Content -->
                    <section class="mb-6" aria-label="Settings">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Settings & Content</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.faqs.index') }}" class="flex items-center px-4 py-3 rounded {{ isActiveRoute('admin.faqs.*') }} transition duration-300 text-base" aria-current="{{ isActiveRoute('admin.faqs.*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-question-circle fa-fw mr-3 {{ request()->routeIs('admin.faqs.*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>FAQ Management</span>
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

                    
                    <!-- Dark Mode Toggle - Mobile -->
                    <section class="mb-6" aria-label="Appearance">
                        <!-- Theme Toggle Switch -->
                        <div class="px-4">
                            <div class="relative inline-flex items-center bg-gray-700 rounded-full p-1 shadow-inner w-full" role="switch" aria-label="Theme toggle">
                                <button type="button" 
                                        onclick="setTheme('light')"
                                        id="admin-light-mode-btn"
                                        class="relative flex items-center justify-center px-6 py-2 rounded-full text-sm font-medium transition-all duration-200 text-gray-300 hover:text-white z-10 flex-1"
                                        role="option"
                                        aria-selected="false">
                                    <i class="fas fa-sun mr-2"></i>
                                    Light
                                </button>
                                <button type="button" 
                                        onclick="setTheme('dark')"
                                        id="admin-dark-mode-btn"
                                        class="relative flex items-center justify-center px-6 py-2 rounded-full text-sm font-medium transition-all duration-200 text-white bg-green-600 shadow-lg z-10 flex-1"
                                        role="option"
                                        aria-selected="true">
                                    <i class="fas fa-moon mr-2"></i>
                                    Dark
                                </button>
                            </div>
                        </div>
                    </section>

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

                    @if($isNurse || $isAdmin || $isSecretary || $isCaptain || $isCouncilor)
                    <!-- QR Code & Attendance Section -->
                    <section class="mb-6" aria-label="QR Code & Attendance">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">QR Code & Attendance</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.attendance.scanner') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.attendance.scanner*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-qrcode fa-fw mr-3 {{ request()->routeIs('admin.attendance.scanner*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>QR Scanner</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.attendance.logs') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.attendance.logs*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-list fa-fw mr-3 {{ request()->routeIs('admin.attendance.logs*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Attendance Logs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.events.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.events*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base">
                                    <i class="fas fa-calendar-check fa-fw mr-3 {{ request()->routeIs('admin.events*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Events</span>
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

                    @if(!$isNurse)
                    <!-- Settings / Content MOBILE -->
                    <section class="mb-6" aria-label="Settings">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Settings & Content</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('admin.faqs.index') }}" class="flex items-center px-4 py-3 rounded {{ request()->routeIs('admin.faqs.*') ? 'bg-green-600 font-medium text-white' : 'hover:bg-gray-300' }} transition duration-300 text-base" aria-current="{{ request()->routeIs('admin.faqs.*') ? 'page' : '' }}">
                                    <i class="fas fa-question-circle fa-fw mr-3 {{ request()->routeIs('admin.faqs.*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>FAQ Management</span>
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

    <!-- Mark All Read Confirmation Modal -->
    <div id="markAllReadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Mark all notifications as read?</h3>
                    <p class="text-sm text-gray-500">This will mark every unread notification as read.</p>
                </div>
            </div>
            <div class="text-gray-700 mb-6">This action cannot be undone.</div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeMarkAllReadModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">Cancel</button>
                <button type="button" onclick="confirmMarkAllRead()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200">Mark All Read</button>
            </div>
        </div>
    </div>

    <script>
        function openMarkAllReadModal() {
            var m = document.getElementById('markAllReadModal');
            if (!m) return;
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeMarkAllReadModal() {
            var m = document.getElementById('markAllReadModal');
            if (!m) return;
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        async function confirmMarkAllRead() {
            closeMarkAllReadModal();
            var f = document.getElementById('adminMarkAllForm');
            if (f) { f.submit(); return; }
            try {
                if (typeof markAllAsRead === 'function') {
                    await markAllAsRead();
                } else if (window.notificationSystem && typeof window.notificationSystem.markAllAsRead === 'function') {
                    await window.notificationSystem.markAllAsRead();
                }
            } catch(e) {}
        }
    </script>
    <form id="adminMarkAllForm" method="POST" action="{{ route('admin.notifications.mark-all-as-read') }}" style="display:none">
        @csrf
    </form>

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
        // Helper to check if user is a nurse
        function isNurseUser() {
            return JSON.parse(`@json($isNurse)`);
        }
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
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
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
                    if (isNurseUser()) {
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
                if (isNurseUser()) {
                    this.showInfo('Nurses can only access health-related notifications');
                    return;
                }
                const notificationElement = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
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
                    if (notificationElement) {
                        notificationElement.style.opacity = '0.5';
                        setTimeout(() => {
                            notificationElement.remove();
                            this.loadNotifications(); // Reload to update counts
                        }, 300);
                    }
                    this.showSuccess('Notification marked as read');
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    this.showError('Failed to mark notification as read');
                });
            },

            // Mark all notifications as read (async/await version)
            markAllAsRead: async function() {
                if (isNurseUser()) {
                    this.showInfo('Nurses can only access health-related notifications');
                    return;
                }
                try {
                    const response = await fetch('/admin/notifications/mark-all-as-read-ajax', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.updateNotificationBadge(0);
                        this.updateNotificationCount(0);
                        this.updateNotificationDropdown([]);
                        this.showSuccess(data.message || 'All notifications marked as read.');
                    } else {
                        this.showError(data.message || 'Failed to mark all as read.');
                    }
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                    this.showError('Failed to mark all notifications as read');
                }
            },

            // Mark notification as viewed (but not read)
            markAsViewed: function(id, type) {
                const notificationElement = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
                if (notificationElement) {
                    notificationElement.classList.add('viewed');
                    notificationElement.style.opacity = '0.8';
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full';
                    notificationElement.style.position = 'relative';
                    notificationElement.appendChild(indicator);
                }
            },

            // View notification details
            viewDetails: function(type, id) {
                if (isNurseUser()) {
                    this.showInfo('Nurses can only access health-related notifications');
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
                        this.showError('Unknown notification type');
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
                    window.location.href = url;
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
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

            // Show success message
            showSuccess: function(message) {
                if (typeof notify === 'function') {
                    notify('success', message);
                } else if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success(message);
                }
            },
            // Show error message
            showError: function(message) {
                if (typeof notify === 'function') {
                    notify('error', message);
                } else if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error(message);
                }
            },
            // Show info message
            showInfo: function(message) {
                if (typeof notify === 'function') {
                    notify('info', message);
                } else if (window.toast && typeof window.toast.info === 'function') {
                    window.toast.info(message);
                }
            },

            // Start polling for new notifications
            startPolling: function() {
                setInterval(() => {
                    this.loadNotifications();
                }, 30000);
            },

            // Bind event listeners
            bindEvents: function() {
                window.markAllAsRead = () => {
                    this.markAllAsRead();
                };
            },

            // Check current page and mark relevant notifications as read
            checkCurrentPageAndMarkNotifications: function() {
                if (isNurseUser()) {
                    return;
                }
                const currentPath = window.location.pathname;
                let notificationType = null;
                if (currentPath.includes('/document-requests')) {
                    notificationType = 'document_request';
                } else if (currentPath.includes('/blotter-reports')) {
                    notificationType = 'blotter_report';
                } else if (currentPath.includes('/new-account-requests')) {
                    notificationType = 'account_request';
                } else if (currentPath.includes('/community-concerns')) {
                    notificationType = 'community_complaint';
                }
                if (notificationType) {
                    this.markNotificationsAsReadByType(notificationType);
                }
            },

            // Mark all notifications of a specific type as read
            markNotificationsAsReadByType: function(type) {
                if (isNurseUser()) {
                    return;
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
    <script>
        (function() {
            try {
                var path = window.location && window.location.pathname ? window.location.pathname : 'root';
                var key = 'skeletonSeen:' + path;
                var skeletons = document.querySelectorAll('[data-skeleton], [id$="Skeleton"]');
                if (skeletons && skeletons.length > 0) {
                    var seen = sessionStorage.getItem(key) === '1';
                    if (seen) {
                        skeletons.forEach(function(el) { el.style.display = 'none'; });
                    } else {
                        sessionStorage.setItem(key, '1');
                    }
                }
            } catch (e) {}

            // helper to clear only our skeleton flags
            window.clearSkeletonFlags = function() {
                try {
                    var keysToRemove = [];
                    for (var i = 0; i < sessionStorage.length; i++) {
                        var k = sessionStorage.key(i);
                        if (!k) continue;
                        if (k.indexOf('skeletonSeen:') === 0 || k.indexOf('GETCACHE:') === 0) keysToRemove.push(k);
                    }
                    keysToRemove.forEach(function(k){ sessionStorage.removeItem(k); });
                } catch(e) {}
            };

            // clear on logout submit
            document.addEventListener('DOMContentLoaded', function() {
                // If we've already seen this page, instantly reveal primary content containers
                if (document.documentElement.classList.contains('skeleton-hide')) {
                    try {
                        var contentNodes = document.querySelectorAll('[id$="Content"], [data-content]');
                        contentNodes.forEach(function(n){ n.style.display = ''; });
                    } catch(e) {}
                }

                var logoutForms = document.querySelectorAll('form[action*="logout"]');
                logoutForms.forEach(function(f){
                    f.addEventListener('submit', function(){
                        if (typeof window.clearSkeletonFlags === 'function') window.clearSkeletonFlags();
                    }, { capture: true });
                });
            });
        })();
    </script>
    <script>
        // Lightweight client-side GET cache with TTL and stale-while-revalidate
        (function(){
            function cacheKey(url){ return 'GETCACHE:' + url; }
            function now(){ return Date.now(); }
            function parse(data){ try { return JSON.parse(data); } catch(e){ return null; } }
            async function fetchAndStore(url, opts){
                const res = await fetch(url, Object.assign({ method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } }, opts && opts.fetch));
                const text = await res.text();
                try { sessionStorage.setItem(cacheKey(url), JSON.stringify({ ts: now(), status: res.status, text: text })); } catch(e) {}
                return { status: res.status, text: text };
            }
            window.cachedGet = async function(url, options){
                const ttlMs = (options && options.ttlMs) || 60000; // 1 min default
                const preferFresh = !!(options && options.preferFresh);
                const onUpdate = options && options.onUpdate;
                const key = cacheKey(url);
                const raw = sessionStorage.getItem(key);
                const cached = raw ? parse(raw) : null;

                const isFresh = cached && (now() - (cached.ts||0) < ttlMs);
                if (preferFresh || !cached) {
                    // fetch fresh; if we have stale cached, return it then update
                    if (cached && onUpdate) { setTimeout(() => onUpdate({ status: cached.status, text: cached.text }), 0); }
                    const fresh = await fetchAndStore(url, options);
                    return fresh;
                } else {
                    // serve cached immediately and refresh in background
                    if (onUpdate) {
                        setTimeout(async () => {
                            try { const fresh = await fetchAndStore(url, options); onUpdate(fresh); } catch(e) {}
                        }, 0);
                    } else {
                        // background refresh without callback
                        fetchAndStore(url, options).catch(function(){});
                    }
                    return { status: cached.status, text: cached.text };
                }
            };
            window.cachedGetJson = async function(url, options){
                const res = await window.cachedGet(url, options);
                try { return { status: res.status, data: JSON.parse(res.text) }; } catch(e) { return { status: res.status, data: null }; }
            };
        })();
    </script>

    <!-- Dark Mode JavaScript -->
    <script>
        // Set theme function
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateToggleButtons(theme);
            console.log('Theme set to:', theme);
        }

        // Update toggle button states
        function updateToggleButtons(theme) {
            const isDark = theme === 'dark';
            
            // Admin desktop toggle
            const adminDesktopLightBtn = document.getElementById('admin-desktop-light-mode-btn');
            const adminDesktopDarkBtn = document.getElementById('admin-desktop-dark-mode-btn');
            
            if (adminDesktopLightBtn && adminDesktopDarkBtn) {
                if (isDark) {
                    adminDesktopLightBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    adminDesktopLightBtn.classList.add('text-gray-300');
                    adminDesktopDarkBtn.classList.remove('text-gray-300');
                    adminDesktopDarkBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                } else {
                    adminDesktopLightBtn.classList.remove('text-gray-300');
                    adminDesktopLightBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                    adminDesktopDarkBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    adminDesktopDarkBtn.classList.add('text-gray-300');
                }
            }
            
            // Admin mobile toggle
            const adminMobileLightBtn = document.getElementById('admin-light-mode-btn');
            const adminMobileDarkBtn = document.getElementById('admin-dark-mode-btn');
            
            if (adminMobileLightBtn && adminMobileDarkBtn) {
                if (isDark) {
                    adminMobileLightBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    adminMobileLightBtn.classList.add('text-gray-300');
                    adminMobileDarkBtn.classList.remove('text-gray-300');
                    adminMobileDarkBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                } else {
                    adminMobileLightBtn.classList.remove('text-gray-300');
                    adminMobileLightBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                    adminMobileDarkBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    adminMobileDarkBtn.classList.add('text-gray-300');
                }
            }
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
            console.log('Theme initialized:', savedTheme);
        });

        // Make setTheme globally available
        window.setTheme = setTheme;
    </script>
    <script>
    function toggleGreenDarkModeSwitch(loc) {
        const theme = localStorage.getItem('theme') === 'dark' ? 'light' : 'dark';
        setTheme(theme);
        updateGreenSwitchUI(theme, loc);
    }
    function updateGreenSwitchUI(t, loc) {
        const id = loc==='admin-desktop'?'admin-desktop-darkmode-switch':'desktop-darkmode-switch';
        const d = document.getElementById(id);
        if(d)d.setAttribute('aria-pressed', (t==="dark").toString());
    }
    document.addEventListener('DOMContentLoaded', function(){
       updateGreenSwitchUI(localStorage.getItem('theme')||'light','admin-desktop');
    });
    </script>
    <script>
function updateDropdownSegmented(loc) {
   const t = document.documentElement.getAttribute('data-theme');
   const light=document.getElementById(loc==='admin-desktop'?"admin-dropdown-light-btn":"dropdown-light-btn"),dark=document.getElementById(loc==='admin-desktop'?"admin-dropdown-dark-btn":"dropdown-dark-btn");
   if(light && dark){
    if(t=='dark') {light.classList.remove('active');dark.classList.add('active');}
    else {dark.classList.remove('active');light.classList.add('active');}
   }
}
document.addEventListener('DOMContentLoaded',function(){updateDropdownSegmented('admin-desktop');});
</script>
</html>