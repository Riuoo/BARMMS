{{-- Stats Skeleton Component --}}
<div class="grid grid-cols-2 lg:grid-cols-2 gap-4 mb-3 animate-pulse">
    @for ($i = 0; $i < 2; $i++)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                </div>
                <div class="ml-4">
                    <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                    <div class="h-6 w-16 bg-gray-300 rounded"></div>
                </div>
            </div>
        </div>
    @endfor
</div>
