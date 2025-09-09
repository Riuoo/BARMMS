{{-- Health Center Activity - Show Page Skeleton --}}
<div class="animate-pulse">
    <!-- Header Skeleton -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="h-8 w-80 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 w-96 bg-gray-100 rounded"></div>
        </div>
        <div class="h-10 w-40 bg-gray-200 rounded"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="w-full h-64 bg-gray-200"></div>
            </div>
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-8 w-64 bg-gray-200 rounded"></div>
                    <div class="flex items-center space-x-3">
                        <div class="h-6 w-32 bg-gray-200 rounded"></div>
                        <div class="h-6 w-20 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="h-4 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-3/4 bg-gray-100 rounded"></div>
                    <div class="h-4 w-1/2 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i = 0; $i < 6; $i++)
                    <div>
                        <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-32 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-4">
                    <div>
                        <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-full bg-gray-100 rounded"></div>
                        <div class="h-4 w-3/4 bg-gray-100 rounded mt-1"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-full bg-gray-100 rounded"></div>
                        <div class="h-4 w-2/3 bg-gray-100 rounded mt-1"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-6 w-32 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-full bg-gray-100 rounded"></div>
                <div class="h-4 w-3/4 bg-gray-100 rounded mt-1"></div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="h-4 w-full bg-gray-100 rounded"></div>
                <div class="h-4 w-2/3 bg-gray-100 rounded mt-1"></div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="h-6 w-20 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>


