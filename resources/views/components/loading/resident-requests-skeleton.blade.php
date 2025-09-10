{{-- Resident Requests Consolidated Skeleton --}}
<div id="residentRequestsSkeleton">
    <!-- Header Skeleton -->
    <div class="animate-pulse mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 w-64 bg-gray-200 rounded"></div>
                <div class="h-4 w-80 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
                <div class="h-10 w-48 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Stats Skeleton -->
    <div class="animate-pulse grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-2">
        @for($i = 0; $i < 5; $i++)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                <div class="ml-3">
                    <div class="h-3 w-24 bg-gray-200 rounded mb-1"></div>
                    <div class="h-6 w-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Filters Skeleton -->
    <div class="animate-pulse mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1"><div class="h-10 w-full bg-gray-200 rounded"></div></div>
            <div class="sm:w-48"><div class="h-10 w-full bg-gray-200 rounded"></div></div>
            <div class="flex space-x-2">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Table Skeleton (Desktop) -->
    <div class="animate-pulse hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-4 gap-4">
                @for($i=0;$i<4;$i++)
                <div class="h-4 w-24 bg-gray-200 rounded"></div>
                @endfor
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @for($i=0;$i<5;$i++)
            <div class="px-6 py-4">
                <div class="grid grid-cols-4 gap-4">
                    @for($j=0;$j<4;$j++)
                    <div class="h-4 w-full bg-gray-200 rounded mb-1"></div>
                    @endfor
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Mobile Cards Skeleton -->
    <div class="animate-pulse md:hidden space-y-4">
        @for($i=0;$i<3;$i++)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    <div class="ml-3">
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-20 bg-gray-100 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="h-3 w-full bg-gray-100 rounded mb-1"></div>
                <div class="h-3 w-24 bg-gray-100 rounded"></div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination Skeleton -->
    <div class="animate-pulse mt-6">
        <div class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
            <div class="h-10 w-20 bg-gray-200 rounded"></div>
            <div class="hidden md:flex space-x-2">
                <div class="h-10 w-8 bg-gray-200 rounded"></div>
                <div class="h-10 w-8 bg-gray-200 rounded"></div>
                <div class="h-10 w-8 bg-gray-200 rounded"></div>
            </div>
            <div class="h-10 w-16 bg-gray-200 rounded"></div>
        </div>
    </div>
</div>


