{{-- Template Cards Skeleton Component --}}
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
