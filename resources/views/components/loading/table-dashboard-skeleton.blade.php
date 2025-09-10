{{-- Consolidated skeleton for table-based dashboard pages --}}
@php
    $variant = $variant ?? null; // e.g., 'due'
    $showSummary = $showSummary ?? false; // optional summary section under table
    $showBreadcrumb = $showBreadcrumb ?? false; // optional breadcrumb row above header
@endphp
<div id="tableDashboardSkeleton">
    {{-- Breadcrumb Skeleton (optional) --}}
    @if($showBreadcrumb)
    <div class="mb-2 animate-pulse">
        <div class="flex items-center space-x-2">
            <div class="h-4 w-40 bg-gray-200 rounded"></div>
            <div class="h-4 w-3 bg-gray-200 rounded"></div>
            <div class="h-4 w-28 bg-gray-100 rounded"></div>
        </div>
    </div>
    @endif
    {{-- Header Skeleton --}}
    <div class="animate-pulse {{ $variant === 'due' ? 'mb-2' : 'mb-3' }}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-10 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-96 bg-gray-100 rounded"></div>
            </div>
            @if (!isset($showButton) || $showButton)
                @php $buttonCount = $buttonCount ?? 1; @endphp
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    @for ($i = 0; $i < $buttonCount; $i++)
                        <div class="h-10 w-40 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            @endif
        </div>
    </div>

    {{-- Filters Skeleton --}}
    <div class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4 animate-pulse">
        @if($variant === 'due')
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="h-5 w-40 bg-gray-200 rounded mb-2"></div>
                <div class="flex flex-wrap items-center gap-2">
                    @for($i=0;$i<4;$i++)
                        <div class="h-8 w-28 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            </div>
        </div>
        @else
        <div class="flex flex-col sm:flex-row gap-4 mb-2">
            <div class="flex-1">
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            <div class="sm:w-48">
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            <div class="sm:w-48">
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            <div class="flex space-x-2">
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Stats Skeleton (optional) --}}
    @if (!isset($showStats) || $showStats)
    <div class="animate-pulse grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
        @for ($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <div class="w-4 h-4 bg-gray-300 rounded"></div>
                        </div>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                        <div class="h-6 w-16 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    @endif

    {{-- Table Skeleton (Desktop) --}}
    <div class="hidden md:block mb-6">
        <div class="animate-pulse space-y-2">
            @for ($i = 0; $i < 8; $i++)
                <div class="flex items-center space-x-4 p-2">
                    <div class="rounded-full bg-gray-300 h-8 w-8"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="h-4 bg-gray-200 rounded w-16"></div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Mobile Cards Skeleton --}}
    <div class="block md:hidden space-y-3 animate-pulse mb-6">
        @for ($i = 0; $i < 4; $i++)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                    <div class="flex-1">
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-24 bg-gray-100 rounded"></div>
                    </div>
                </div>
                <div class="h-4 w-40 bg-gray-100 rounded mb-2"></div>
                <div class="h-4 w-24 bg-gray-200 rounded"></div>
            </div>
        @endfor
    </div>

    {{-- Pagination Skeleton --}}
    <div class="mt-6">
        <div class="animate-pulse flex space-x-2 justify-center mt-4">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-8 w-12 bg-gray-300 rounded"></div>
            @endfor
        </div>
    </div>

    {{-- Optional Summary Skeleton (e.g., for Due Vaccinations) --}}
    @if($showSummary)
    <div class="mt-2">
        <div class="animate-pulse grid grid-cols-1 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-center flex-1">
                        <div class="h-8 w-12 bg-gray-200 rounded mx-auto mb-2"></div>
                        <div class="h-4 w-20 bg-gray-200 rounded mx-auto"></div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="h-8 w-12 bg-gray-200 rounded mx-auto mb-2"></div>
                        <div class="h-4 w-20 bg-gray-200 rounded mx-auto"></div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="h-8 w-12 bg-gray-200 rounded mx-auto mb-2"></div>
                        <div class="h-4 w-20 bg-gray-200 rounded mx-auto"></div>
                    </div>
                    <div class="text-center flex-1">
                        <div class="h-8 w-12 bg-gray-200 rounded mx-auto mb-2"></div>
                        <div class="h-4 w-20 bg-gray-200 rounded mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
