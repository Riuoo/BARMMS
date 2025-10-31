{{-- Resident Dashboard Consolidated Skeleton --}}
<div id="residentDashboardSkeleton" class="animate-pulse" data-skeleton>
    <!-- Header Skeleton -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-8 w-64 bg-gray-200 rounded"></div>
                <div class="h-4 w-80 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="bg-gray-200 rounded-lg px-4 py-2 w-56 h-10"></div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Skeleton -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-2">
        @for ($i = 0; $i < 4; $i++)
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 flex flex-col justify-between h-32">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                    <div class="h-8 w-16 bg-gray-300 rounded"></div>
                </div>
                <div class="bg-gray-200 rounded-full w-10 h-10"></div>
            </div>
            <div class="h-4 w-20 bg-gray-100 rounded"></div>
        </div>
        @endfor
    </div>

    <!-- Recent Activity Skeleton -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-2">
        <div class="h-6 w-40 bg-gray-200 rounded mb-2"></div>
        <div class="space-y-3">
            @for($i = 0; $i < 4; $i++)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                    <div class="ml-4">
                        <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                        <div class="h-3 w-56 bg-gray-100 rounded"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="h-5 w-20 bg-gray-200 rounded-full"></div>
                    <div class="h-3 w-24 bg-gray-100 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- FAB Skeleton -->
    <div class="fixed bottom-6 right-6 z-40">
        <div class="w-14 h-14 bg-gray-200 rounded-full shadow"></div>
    </div>
</div>


