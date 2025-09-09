{{-- Medicine Form Skeleton Component --}}
<div class="animate-pulse bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
    <!-- Medicine Information Section -->
    <div class="border-b border-gray-200 pb-6">
        <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @for($i = 0; $i < 2; $i++)
            <div>
                <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
                <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
            </div>
            @endfor
            <div>
                <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
                <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
            </div>
            <div>
                <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
                <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Stock Information Section -->
    <div class="border-b border-gray-200 pb-6">
        <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @for($i = 0; $i < 3; $i++)
            <div>
                <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
                <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Additional Information Section -->
    <div>
        <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
        <div>
            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
            <div class="h-20 w-full bg-gray-200 rounded"></div>
            <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
        <div class="h-4 w-80 bg-gray-200 rounded"></div>
        <div class="flex space-x-3">
            <div class="h-10 w-24 bg-gray-200 rounded"></div>
            <div class="h-10 w-32 bg-gray-200 rounded"></div>
        </div>
    </div>
</div>


