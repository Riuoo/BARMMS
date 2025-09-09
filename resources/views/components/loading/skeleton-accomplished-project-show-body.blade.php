{{-- Accomplished Project Show Body Skeleton --}}
<div class="animate-pulse">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="w-full h-64 bg-gray-200 rounded-xl"></div>
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-full bg-gray-100 rounded mb-2"></div>
                <div class="h-4 w-5/6 bg-gray-100 rounded"></div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for ($i = 0; $i < 6; $i++)
                    <div>
                        <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-40 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-5 w-56 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="h-4 w-full bg-gray-100 rounded"></div>
                    <div class="h-4 w-5/6 bg-gray-100 rounded"></div>
                    <div class="h-4 w-2/3 bg-gray-100 rounded"></div>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="h-4 w-48 bg-gray-100 rounded"></div>
                    <div class="h-4 w-40 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-5 w-28 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="h-9 w-full bg-gray-200 rounded"></div>
                    <div class="h-9 w-full bg-gray-200 rounded"></div>
                    <div class="h-9 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>
