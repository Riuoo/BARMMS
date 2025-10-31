{{-- Resident Profile Consolidated Skeleton --}}
<div id="residentProfileSkeleton" class="animate-pulse" data-skeleton>
    <!-- Header Skeleton -->
    <div class="mb-2">
        <div class="h-8 w-64 bg-gray-200 rounded"></div>
        <div class="h-4 w-96 bg-gray-100 rounded"></div>
    </div>

    <!-- Cards Skeleton -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                <div class="ml-4">
                    <div class="h-6 w-40 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-32 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="space-y-4">
                @for($i=0;$i<4;$i++)
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                @endfor
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                <div class="ml-4">
                    <div class="h-6 w-48 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="space-y-4">
                @for($i=0;$i<3;$i++)
                <div>
                    <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                @endfor
                <div class="pt-4">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-2">
            <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
            <div class="ml-4">
                <div class="h-6 w-40 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-44 bg-gray-100 rounded"></div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @for($i=0;$i<4;$i++)
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                <div class="h-3 w-48 bg-gray-100 rounded"></div>
            </div>
            @endfor
        </div>
    </div>

    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex">
            <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
            <div class="flex-1">
                <div class="h-5 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="space-y-1">
                    @for($i=0;$i<5;$i++)
                    <div class="h-4 w-80 bg-gray-100 rounded"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>


