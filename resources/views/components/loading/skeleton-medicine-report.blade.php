{{-- Medicine Report Page Skeleton Component --}}
<div class="animate-pulse">
    <!-- Header Skeleton -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-8 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-96 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Filters Skeleton -->
    <div class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div class="h-10 w-full bg-gray-200 rounded"></div>
            <div class="h-10 w-full bg-gray-200 rounded"></div>
            <div class="flex space-x-2">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Charts Skeleton Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-24 bg-gray-100 rounded mb-4"></div>
            <div class="h-64 w-full bg-gray-200 rounded"></div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-24 bg-gray-100 rounded mb-4"></div>
            <div class="h-48 w-full bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Charts Skeleton Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        @for($i = 0; $i < 3; $i++)
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-24 bg-gray-100 rounded mb-4"></div>
            <div class="h-56 w-full bg-gray-200 rounded"></div>
        </div>
        @endfor
    </div>

    <!-- Category Distribution Skeleton -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
            <div class="h-56 w-full bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Optimization Summary Skeleton -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 w-48 bg-gray-200 rounded"></div>
                <div class="h-3 w-80 bg-gray-100 rounded"></div>
                <div class="h-3 w-72 bg-gray-100 rounded"></div>
                <div class="h-3 w-64 bg-gray-100 rounded"></div>
                <div class="h-3 w-56 bg-gray-100 rounded"></div>
            </div>
        </div>
    </div>
</div>


