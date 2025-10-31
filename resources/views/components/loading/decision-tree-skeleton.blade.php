{{-- Decision Tree Dashboard Skeleton Component --}}
<div class="animate-pulse space-y-6" data-skeleton>
    <!-- Header (title, subtitle, actions) -->
    <div class="mb-2">
        <div class="flex items-start justify-between">
            <div>
                <div class="h-10 w-96 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-[36rem] max-w-[80vw] bg-gray-100 rounded"></div>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
                <div class="h-10 w-36 bg-gray-200 rounded"></div>
                <div class="h-10 w-36 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- KPI Cards (4) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @for ($i = 0; $i < 4; $i++)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-7 w-20 bg-gray-300 rounded"></div>
                </div>
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
            </div>
            <div class="mt-2 h-3 w-16 bg-gray-100 rounded"></div>
        </div>
        @endfor
    </div>

    <!-- Two Analysis Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Service Eligibility Analysis -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
                    <div class="h-6 w-24 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="h-20 bg-gray-200 rounded"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                </div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Health Risk Assessment -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-6 w-52 bg-gray-200 rounded"></div>
                    <div class="h-6 w-28 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="h-16 bg-gray-200 rounded"></div>
                    <div class="h-16 bg-gray-200 rounded"></div>
                    <div class="h-16 bg-gray-200 rounded"></div>
                </div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Program Recommendations -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="h-6 w-64 bg-gray-200 rounded"></div>
                <div class="h-6 w-28 bg-gray-200 rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @for ($i = 0; $i < 3; $i++)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div class="h-5 w-44 bg-gray-200 rounded"></div>
                        <div class="h-6 w-24 bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-4 w-56 bg-gray-200 rounded"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Detailed Analysis Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="h-6 w-64 bg-gray-200 rounded"></div>
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-48 bg-gray-200 rounded"></div>
                    <div class="h-9 w-44 bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <div class="h-10 bg-gray-100 rounded mb-2"></div>
                    @for ($i = 0; $i < 6; $i++)
                    <div class="h-12 bg-gray-200 rounded mb-2"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-5">
            <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-3">
                    @for ($i = 0; $i < 3; $i++)
                    <div class="p-4 border-l-4 border-gray-300 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="h-5 w-64 bg-gray-200 rounded"></div>
                            <div class="h-6 w-16 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    @endfor
                </div>
                <div class="space-y-3">
                    @for ($i = 0; $i < 3; $i++)
                    <div class="p-4 border-l-4 border-gray-300 bg-gray-50 rounded-lg">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-gray-200 rounded-full mr-3"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-5 w-56 bg-gray-200 rounded"></div>
                                <div class="h-4 w-72 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>


