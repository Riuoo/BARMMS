{{-- Consolidated Notifications Skeleton --}}
<div id="adminNotifSkeleton">
    <!-- Header Skeleton -->
    <div class="animate-pulse mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 w-64 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-80 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="h-5 w-40 bg-gray-200 rounded mb-2 sm:mb-0"></div>
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Filters Skeleton -->
    <div class="animate-pulse mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1"><div class="h-10 w-full bg-gray-200 rounded"></div></div>
            <div class="sm:w-48"><div class="h-10 w-full bg-gray-200 rounded"></div></div>
            <div class="flex space-x-2">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Table Skeleton -->
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-4 gap-4">
                @for($i=0;$i<4;$i++)
                <div class="h-4 w-24 bg-gray-200 rounded"></div>
                @endfor
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @for($i=0;$i<6;$i++)
            <div class="px-6 py-4">
                <div class="grid grid-cols-4 gap-4 items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="h-4 w-60 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-4 w-24 bg-gray-100 rounded"></div>
                    <div class="h-5 w-20 bg-gray-200 rounded-full"></div>
                    <div class="h-8 w-20 bg-gray-200 rounded justify-self-center"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>