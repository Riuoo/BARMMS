<!-- Resident Announcements Skeleton -->
<div class="animate-pulse">
    <!-- Header Skeleton -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 bg-gray-200 rounded w-80 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-96"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="h-10 bg-gray-200 rounded w-32"></div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Skeleton -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="ml-3">
                    <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
                    <div class="h-6 bg-gray-200 rounded w-8"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="ml-3">
                    <div class="h-4 bg-gray-200 rounded w-20 mb-2"></div>
                    <div class="h-6 bg-gray-200 rounded w-8"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="ml-3">
                    <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
                    <div class="h-6 bg-gray-200 rounded w-8"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="ml-3">
                    <div class="h-4 bg-gray-200 rounded w-20 mb-2"></div>
                    <div class="h-6 bg-gray-200 rounded w-8"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Skeleton -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="h-10 bg-gray-200 rounded"></div>
            </div>
            <div class="sm:w-48">
                <div class="h-10 bg-gray-200 rounded"></div>
            </div>
            <div class="sm:w-48">
                <div class="h-10 bg-gray-200 rounded"></div>
            </div>
            <div class="flex space-x-2">
                <div class="h-10 bg-gray-200 rounded w-20"></div>
                <div class="h-10 bg-gray-200 rounded w-20"></div>
            </div>
        </div>
    </div>

    <!-- Quick Filter Buttons Skeleton -->
    <div class="flex flex-wrap gap-2 mb-6">
        <div class="h-8 bg-gray-200 rounded-lg w-24"></div>
        <div class="h-8 bg-gray-200 rounded-lg w-28"></div>
        <div class="h-8 bg-gray-200 rounded-lg w-32"></div>
    </div>

    <!-- Grid Skeleton -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for($i = 0; $i < 6; $i++)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="h-48 bg-gray-200"></div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-6 bg-gray-200 rounded w-20"></div>
                    <div class="h-6 bg-gray-200 rounded w-16"></div>
                </div>
                <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-full mb-1"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3 mb-4"></div>
                <div class="flex items-center justify-between">
                    <div class="h-6 bg-gray-200 rounded w-16"></div>
                    <div class="h-6 bg-gray-200 rounded w-20"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
