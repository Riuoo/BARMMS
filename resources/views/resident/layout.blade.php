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
    <title>@yield('title', 'Resident Page')</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css'])
    @notifyCss
    <style>
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

        /* HIGH SPECIFICITY: Force colored buttons to keep their backgrounds in dark mode */
        
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
        /* Dashboard card icon backgrounds - keep colored in dark mode */
        /* Single color backgrounds */
        [data-theme="dark"] .rounded-full.bg-green-100   { background-color: #d1fae5 !important; color: #059669 !important; }
        [data-theme="dark"] .rounded-full.bg-green-200   { background-color: #a7f3d0 !important; color: #047857 !important; }
        [data-theme="dark"] .rounded-full.bg-blue-100    { background-color: #dbeafe !important; color: #2563eb !important; }
        [data-theme="dark"] .rounded-full.bg-blue-200    { background-color: #bfdbfe !important; color: #1d4ed8 !important; }
        [data-theme="dark"] .rounded-full.bg-yellow-100  { background-color: #fef9c3 !important; color: #b45309 !important; }
        [data-theme="dark"] .rounded-full.bg-yellow-200  { background-color: #fef08a !important; color: #b45309 !important; }
        [data-theme="dark"] .rounded-full.bg-red-100     { background-color: #fee2e2 !important; color: #dc2626 !important; }
        [data-theme="dark"] .rounded-full.bg-red-200     { background-color: #fecaca !important; color: #b91c1c !important; }
        [data-theme="dark"] .rounded-full.bg-purple-100  { background-color: #ede9fe !important; color: #9333ea !important; }
        [data-theme="dark"] .rounded-full.bg-orange-100  { background-color: #ffedd5 !important; color: #ea580c !important; }
        [data-theme="dark"] .rounded-full.bg-pink-100    { background-color: #fce7f3 !important; color: #db2777 !important; }
        [data-theme="dark"] .rounded-full.bg-teal-100    { background-color: #ccfbf1 !important; color: #0d9488 !important; }
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

        /* Match admin notification dropdown scroll styling */
        .notification-scroll::-webkit-scrollbar { width: 6px; }
        .notification-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
        .notification-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .notification-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        
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

        .darkmode-switch-btn {
          padding: 0;
          border: none;
          outline: none;
          box-shadow: none;
          cursor: pointer;
          background: #aaa !important;
          transition: background 0.2s;
        }
        .darkmode-switch-btn[aria-pressed='true'] {
          background-color: #ef6c1d !important;
        }
        .darkmode-switch-btn[aria-pressed='false'] {
          background-color: #aaa !important;
        }
        .darkmode-switch-btn .darkmode-switch-thumb {
          transition: transform 0.2s;
        }
        .darkmode-switch-btn[aria-pressed='true'] .darkmode-switch-thumb {
          transform: translateX(28px);
        }
        .darkmode-switch-btn[aria-pressed='false'] .darkmode-switch-thumb {
          transform: translateX(0px);
        }
        .segment-btn {background:transparent;border:none;color:#ddd;box-shadow:none;height:100%;}
        .segment-btn.active {background:#ef6c1d;color:#fff;}
        .segment-btn:focus {outline:none;}
        .menu-icon { font-size: 1rem; display:inline-block; vertical-align:middle; }
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
</head>
<body x-data="{ sidebarOpen: false }" class="flex flex-col min-h-screen bg-gray-100 font-sans" data-notify-timeout="{{ config('notify.timeout', 5000) }}" data-notifications-url="{{ route('resident.notifications.count') }}" data-theme="light">
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
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2"
                                         :class="n.type === 'blotter_request' ? 'bg-red-100' : 'bg-blue-100'">
                                        <i :class="n.type === 'blotter_request' ? 'fas fa-gavel text-red-600 text-sm' : 'fas fa-file-signature text-blue-600 text-sm'"></i>
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
                            <form id="residentMarkAllForm" method="POST" action="{{ route('resident.notifications.mark-all') }}">
                                @csrf
                                <button type="button" onclick="openResidentMarkAllReadModal()" class="text-gray-600 hover:text-gray-800 text-sm transition duration-200">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Mark All Read
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                    <span class="font-semibold hidden sm:inline">{{ $currentUser->name ?? 'Resident' }}</span>
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
                        <a href="{{ route('resident.profile') }}"
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
                            <button id="desktop-darkmode-switch" type="button"
                                class="relative w-8 h-4.5 rounded-full focus:outline-none darkmode-switch-btn border-none"
                                aria-pressed="false"
                                onclick="toggleGreenDarkModeSwitch('desktop')">
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

                    <!-- Community -->
                    <section class="mb-6" aria-label="Community">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Community</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.announcements') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.announcements*') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.announcements*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-bullhorn fa-fw mr-3 {{ request()->routeIs('resident.announcements*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Bulletin Board</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.faqs') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.faqs') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.faqs') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-question-circle fa-fw mr-3 {{ request()->routeIs('resident.faqs') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>FAQ & Quick Help</span>
                                </a>
                            </li>
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

                    <!-- Dark Mode Toggle - Mobile -->
                    <section class="mb-6" aria-label="Appearance">
                        <!-- Theme Toggle Switch -->
                        <div class="px-4">
                            <div class="relative inline-flex items-center bg-gray-700 rounded-full p-1 shadow-inner w-full" role="switch" aria-label="Theme toggle">
                                <button type="button" 
                                        onclick="setTheme('light')"
                                        id="light-mode-btn"
                                        class="relative flex items-center justify-center px-6 py-2 rounded-full text-sm font-medium transition-all duration-200 text-gray-300 hover:text-white z-10 flex-1"
                                        role="option"
                                        aria-selected="false">
                                    <i class="fas fa-sun mr-2"></i>
                                    Light
                                </button>
                                <button type="button" 
                                        onclick="setTheme('dark')"
                                        id="dark-mode-btn"
                                        class="relative flex items-center justify-center px-6 py-2 rounded-full text-sm font-medium transition-all duration-200 text-white bg-green-600 shadow-lg z-10 flex-1"
                                        role="option"
                                        aria-selected="true">
                                    <i class="fas fa-moon mr-2"></i>
                                    Dark
                                </button>
                            </div>
                        </div>
                    </section>

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

                    <!-- Community -->
                    <section class="mb-6" aria-label="Community">
                        <h3 class="text-gray-400 uppercase tracking-wide text-xs font-semibold mb-2 px-4">Community</h3>
                        <ul class="flex flex-col space-y-2">
                            <li>
                                <a href="{{ route('resident.announcements') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.announcements*') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.announcements*') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-bullhorn fa-fw mr-3 {{ request()->routeIs('resident.announcements*') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>Bulletin Board</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('resident.faqs') }}" class="flex items-center px-4 py-3 rounded {{ isActiveResidentRoute('resident.faqs') }} transition duration-300 text-base" aria-current="{{ isActiveResidentRoute('resident.faqs') == 'bg-green-600 font-medium text-white' ? 'page' : '' }}">
                                    <i class="fas fa-question-circle fa-fw mr-3 {{ request()->routeIs('resident.faqs') ? 'text-white' : 'text-green-600' }}" aria-hidden="true"></i>
                                    <span>FAQ & Quick Help</span>
                                </a>
                            </li>
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
                        // Instant reveal for content wrappers
                        try {
                            var contentNodes = document.querySelectorAll('[id$="Content"], [data-content]');
                            contentNodes.forEach(function(n){ n.style.display = ''; });
                        } catch(e) {}
                    } else {
                        sessionStorage.setItem(key, '1');
                    }
                }
            } catch (e) {}

            // expose helper
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
                const ttlMs = (options && options.ttlMs) || 60000;
                const preferFresh = !!(options && options.preferFresh);
                const onUpdate = options && options.onUpdate;
                const key = cacheKey(url);
                const raw = sessionStorage.getItem(key);
                const cached = raw ? parse(raw) : null;

                const isFresh = cached && (now() - (cached.ts||0) < ttlMs);
                if (preferFresh || !cached) {
                    if (cached && onUpdate) { setTimeout(() => onUpdate({ status: cached.status, text: cached.text }), 0); }
                    const fresh = await fetchAndStore(url, options);
                    return fresh;
                } else {
                    if (onUpdate) {
                        setTimeout(async () => {
                            try { const fresh = await fetchAndStore(url, options); onUpdate(fresh); } catch(e) {}
                        }, 0);
                    } else {
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
    <!-- Resident Mark All Read Confirmation Modal -->
    <div id="residentMarkAllReadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999]">
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
                <button type="button" onclick="closeResidentMarkAllReadModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">Cancel</button>
                <button type="button" onclick="confirmResidentMarkAllRead()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition duration-200">Mark All Read</button>
            </div>
        </div>
    </div>

    <script>
        function openResidentMarkAllReadModal() {
            var m = document.getElementById('residentMarkAllReadModal');
            if (!m) return;
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeResidentMarkAllReadModal() {
            var m = document.getElementById('residentMarkAllReadModal');
            if (!m) return;
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        function confirmResidentMarkAllRead() {
            closeResidentMarkAllReadModal();
            var f = document.getElementById('residentMarkAllForm');
            try { localStorage.setItem('residentMarkAllReadNotify', '1'); } catch(e) {}
            if (f) f.submit();
        }
    </script>
    <script>
        // Show toast after redirect when resident mark-all succeeds
        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (localStorage.getItem('residentMarkAllReadNotify') === '1') {
                    if (typeof notify === 'function') {
                        notify('success', 'All notifications marked as read.');
                    } else if (window.toast && typeof window.toast.success === 'function') {
                        window.toast.success('All notifications marked as read.');
                    }
                    localStorage.removeItem('residentMarkAllReadNotify');
                }
            } catch(e) {}
        });
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
            
            // Desktop toggle
            const desktopLightBtn = document.getElementById('desktop-light-mode-btn');
            const desktopDarkBtn = document.getElementById('desktop-dark-mode-btn');
            
            if (desktopLightBtn && desktopDarkBtn) {
                if (isDark) {
                    desktopLightBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    desktopLightBtn.classList.add('text-gray-300');
                    desktopDarkBtn.classList.remove('text-gray-300');
                    desktopDarkBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                } else {
                    desktopLightBtn.classList.remove('text-gray-300');
                    desktopLightBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                    desktopDarkBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    desktopDarkBtn.classList.add('text-gray-300');
                }
            }
            
            // Mobile toggle
            const mobileLightBtn = document.getElementById('light-mode-btn');
            const mobileDarkBtn = document.getElementById('dark-mode-btn');
            
            if (mobileLightBtn && mobileDarkBtn) {
                if (isDark) {
                    mobileLightBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    mobileLightBtn.classList.add('text-gray-300');
                    mobileDarkBtn.classList.remove('text-gray-300');
                    mobileDarkBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                } else {
                    mobileLightBtn.classList.remove('text-gray-300');
                    mobileLightBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
                    mobileDarkBtn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
                    mobileDarkBtn.classList.add('text-gray-300');
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
    function toggleDarkModeSwitch(loc) {
        const theme = localStorage.getItem('theme') === 'dark' ? 'light' : 'dark';
        setTheme(theme);
        updateSwitchUI(theme, loc);
    }
    function updateSwitchUI(t, loc) {
        const d = document.getElementById(loc==='desktop' ? 'desktop-darkmode-switch' : 'admin-desktop-darkmode-switch');
        if(d)d.setAttribute('aria-pressed', (t==="dark").toString());
    }
    document.addEventListener('DOMContentLoaded', function(){
       updateSwitchUI(localStorage.getItem('theme')||'light','desktop');
    });
    </script>
    <script>
    function updateDropdownSegmented(loc) {
       const t = document.documentElement.getAttribute('data-theme');
       const light=document.getElementById(loc==='desktop'?"dropdown-light-btn":"admin-dropdown-light-btn"),dark=document.getElementById(loc==='desktop'?"dropdown-dark-btn":"admin-dropdown-dark-btn");
       if(light && dark){
        if(t=='dark') {light.classList.remove('active');dark.classList.add('active');}
        else {dark.classList.remove('active');light.classList.add('active');}
       }
    }
    document.addEventListener('DOMContentLoaded',function(){updateDropdownSegmented('desktop');});
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
       updateGreenSwitchUI(localStorage.getItem('theme')||'light','desktop');
    });
    </script>
</body>
</html>