{{-- Consolidated skeleton for grid-based dashboard pages --}}
<div id="gridDashboardSkeleton" class="animate-pulse" data-skeleton>
    {{-- Header Skeleton --}}
    <div class="mb-3 animate-pulse">
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
    </div>

    {{-- Stats Skeleton --}}
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

    {{-- Warning Skeleton (conditional) --}}
    @if(isset($showWarning) && $showWarning)
    <div class="animate-pulse mb-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="w-5 h-5 bg-gray-200 rounded mr-3"></div>
            <div class="flex-1">
                <div class="h-4 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-96 bg-gray-100 rounded"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Grid Skeleton (for activities/projects) --}}
    @if(isset($gridType) && $gridType === 'activities')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-2 animate-pulse">
        @for ($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="w-full h-48 bg-gray-200"></div>
                <div class="p-5">
                    <div class="h-6 w-48 bg-gray-200 rounded mb-3"></div>
                    <div class="h-4 w-full bg-gray-100 rounded mb-2"></div>
                    <div class="h-4 w-3/4 bg-gray-100 rounded mb-4"></div>
                    <div class="space-y-2 mb-4">
                        <div class="h-3 w-32 bg-gray-200 rounded"></div>
                        <div class="h-3 w-28 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-2 mb-5">
                        <div class="h-3 w-40 bg-gray-200 rounded"></div>
                        <div class="h-3 w-36 bg-gray-200 rounded"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    @endif

    {{-- Template Cards Skeleton (for templates) --}}
    @if(isset($gridType) && $gridType === 'templates')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 animate-pulse">
        @for ($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4">
                    <!-- Template Header Skeleton -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-full mr-3"></div>
                            <div>
                                <div class="h-5 w-32 bg-gray-200 rounded mb-2"></div>
                                <div class="h-6 w-20 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Template Info Skeleton -->
                    <div class="space-y-3 mb-3">
                        <div class="flex items-center">
                            <div class="h-4 w-32 bg-gray-200 rounded"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="h-4 w-28 bg-gray-200 rounded"></div>
                        </div>
                        <div class="h-4 w-48 bg-gray-200 rounded"></div>
                    </div>

                    <!-- Action Buttons Skeleton -->
                    <div class="flex flex-wrap gap-2">
                        <div class="h-8 w-20 bg-gray-200 rounded"></div>
                        <div class="h-8 w-24 bg-gray-200 rounded"></div>
                        <div class="h-8 w-20 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    @endif

    {{-- Default Grid Skeleton (fallback) --}}
    @if(!isset($gridType))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-2 animate-pulse">
        @for ($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="w-full h-48 bg-gray-200"></div>
                <div class="p-5">
                    <div class="h-6 w-48 bg-gray-200 rounded mb-3"></div>
                    <div class="h-4 w-full bg-gray-100 rounded mb-2"></div>
                    <div class="h-4 w-3/4 bg-gray-100 rounded mb-4"></div>
                    <div class="space-y-2 mb-4">
                        <div class="h-3 w-32 bg-gray-200 rounded"></div>
                        <div class="h-3 w-28 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-2 mb-5">
                        <div class="h-3 w-40 bg-gray-200 rounded"></div>
                        <div class="h-3 w-36 bg-gray-200 rounded"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                        <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    @endif

    {{-- Pagination Skeleton --}}
    <div class="mt-6">
        <div class="animate-pulse flex space-x-2 justify-center mt-4">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-8 w-12 bg-gray-300 rounded"></div>
            @endfor
        </div>
    </div>
</div>
