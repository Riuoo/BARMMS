{{-- Document Analysis Dashboard Skeleton Component --}}
<div class="animate-pulse">
    <!-- Header Skeleton -->
    <div class="mb-6">
        <div class="text-center">
            <div class="h-8 w-80 bg-gray-200 rounded mb-3 mx-auto"></div>
            <div class="h-5 w-96 bg-gray-100 rounded mb-4 mx-auto"></div>
            
            <!-- Controls Skeleton -->
            <div class="flex flex-wrap justify-center gap-3 mb-4">
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
                <div class="h-10 w-36 bg-gray-200 rounded"></div>
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
                <div class="h-10 w-24 bg-gray-200 rounded"></div>
            </div>
            
            <!-- Settings Skeleton -->
            <div class="flex flex-wrap justify-center gap-2 text-sm">
                <div class="h-6 w-32 bg-gray-200 rounded"></div>
                <div class="h-6 w-24 bg-gray-200 rounded"></div>
                <div class="h-6 w-28 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Stats Skeleton -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-3">
        @for ($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                    </div>
                    <div class="ml-3">
                        <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                        <div class="h-6 w-16 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Charts Skeleton -->
    <div class="space-y-6 mb-3">
        <!-- Purok Chart Skeleton -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                    <div class="h-8 w-24 bg-gray-200 rounded"></div>
                </div>
                <div class="h-96 w-full bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Cluster Chart Skeleton -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
                    <div class="h-8 w-24 bg-gray-200 rounded"></div>
                </div>
                <div class="h-96 w-full bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Analysis Tables Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Purok Analysis Skeleton -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="h-4 w-20 bg-gray-200 rounded"></div>
                                <div class="h-4 w-16 bg-gray-200 rounded"></div>
                                <div class="h-4 w-12 bg-gray-200 rounded"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Cluster Analysis Skeleton -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-4">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                                    <div class="h-6 w-20 bg-gray-200 rounded"></div>
                                </div>
                                <div class="h-4 w-48 bg-gray-200 rounded"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Skeleton -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="h-6 w-32 bg-gray-200 rounded mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="h-5 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-48 bg-gray-200 rounded"></div>
                            <div class="h-4 w-52 bg-gray-200 rounded"></div>
                            <div class="h-4 w-44 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="h-5 w-40 bg-gray-200 rounded mb-2"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-56 bg-gray-200 rounded"></div>
                            <div class="h-4 w-52 bg-gray-200 rounded"></div>
                            <div class="h-4 w-48 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
