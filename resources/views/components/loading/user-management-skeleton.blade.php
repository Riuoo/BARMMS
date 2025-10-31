<div id="userManagementSkeleton" class="animate-pulse" data-skeleton>
    {{-- Header Skeleton --}}
    <div class="mb-3 animate-pulse">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-10 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-96 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <!-- Primary action button placeholder -->
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    {{-- Stats Skeleton --}}
    <div class="animate-pulse grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
        @for ($i = 0; $i < 4; $i++)
            <div class="bg-gray-200 rounded-lg p-4 flex flex-col items-start">
                <div class="h-8 w-8 bg-gray-300 rounded-full mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-3/4 mb-1"></div>
                <div class="h-6 bg-gray-300 rounded w-1/2"></div>
            </div>
        @endfor
    </div>

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
</div>
