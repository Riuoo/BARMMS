{{-- Due Vaccinations Summary Skeleton Component --}}
<div class="animate-pulse mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="text-center">
            <div class="h-8 w-8 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-20 bg-gray-200 rounded"></div>
        </div>
        @endfor
    </div>
</div>
