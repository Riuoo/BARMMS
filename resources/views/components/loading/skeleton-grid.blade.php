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
