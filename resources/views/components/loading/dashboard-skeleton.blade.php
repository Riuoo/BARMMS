{{-- Unified Dashboard Skeleton Component --}}
@php
    $variant = $variant ?? 'admin'; // 'admin' | 'health'
@endphp

@if($variant === 'health')
    {{-- Health Dashboard Skeleton (migrated from skeleton-health-dashboard) --}}
    <div class="animate-pulse" id="healthDashboardSkeleton">
        <!-- Header Skeleton -->
        <div class="mb-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="h-8 w-72 bg-gray-200 rounded mb-1.5"></div>
                    <div class="h-4 w-96 bg-gray-100 rounded"></div>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    <div class="h-8 w-56 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Skeleton -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-2">
            @for ($i = 0; $i < 4; $i++)
            <div class="rounded-lg overflow-hidden shadow-md">
                <div class="p-4 bg-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="h-3 w-20 bg-gray-100 rounded mb-2"></div>
                            <div class="h-6 w-16 bg-gray-100 rounded"></div>
                        </div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full"></div>
                    </div>
                    <div class="mt-2 h-3 w-16 bg-gray-100 rounded"></div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Alerts Skeleton -->
        <div class="mb-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-lg shadow bg-gray-100 border-l-4 border-gray-300">
                    <div class="flex items-center mb-2">
                        <div class="w-5 h-5 bg-gray-200 rounded mr-2"></div>
                        <div class="h-4 w-40 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-2 ml-6">
                        <div class="h-3 w-full bg-gray-200 rounded"></div>
                        <div class="h-3 w-3/4 bg-gray-200 rounded"></div>
                        <div class="h-3 w-1/2 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Skeleton -->
        <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 gap-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <div class="h-5 w-48 bg-gray-200 rounded mb-2"></div>
                    <div class="w-full h-48 bg-gray-100 rounded"></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <div class="h-5 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="w-full h-48 bg-gray-100 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Medicine Analytics Skeleton -->
        <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 gap-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <div class="h-5 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div class="text-center p-3 bg-gray-100 rounded-lg">
                            <div class="h-6 w-8 bg-gray-200 rounded mb-1"></div>
                            <div class="h-3 w-16 bg-gray-200 rounded"></div>
                        </div>
                        <div class="text-center p-3 bg-gray-100 rounded-lg">
                            <div class="h-6 w-8 bg-gray-200 rounded mb-1"></div>
                            <div class="h-3 w-20 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @for ($i = 0; $i < 4; $i++)
                        <div class="flex justify-between items-center p-3 bg-gray-100 rounded-lg">
                            <div class="flex-1">
                                <div class="h-3 w-32 bg-gray-200 rounded mb-1"></div>
                                <div class="h-3 w-24 bg-gray-200 rounded"></div>
                            </div>
                            <div class="h-6 w-12 bg-gray-200 rounded"></div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <div class="h-5 w-64 bg-gray-200 rounded mb-2"></div>
                    <div class="w-full h-48 bg-gray-100 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Skeleton -->
        <div class="mt-2 grid grid-cols-1 lg:grid-cols-3 gap-2">
            @for ($i = 0; $i < 3; $i++)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-4">
                    <div class="h-5 w-40 bg-gray-200 rounded mb-2"></div>
                    <div class="space-y-3">
                        @for ($j = 0; $j < 4; $j++)
                        <div class="border-l-4 border-gray-300 pl-4">
                            <div class="h-3 w-32 bg-gray-200 rounded mb-1"></div>
                            <div class="h-3 w-full bg-gray-100 rounded mb-1"></div>
                            <div class="h-3 w-20 bg-gray-100 rounded"></div>
                        </div>
                        @endfor
                    </div>
                    <div class="mt-4">
                        <div class="h-3 w-32 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <!-- FAB Skeleton -->
        <div class="fixed bottom-6 right-6 z-40">
            <div class="w-14 h-14 bg-gray-200 rounded-full"></div>
        </div>
    </div>
@else
    {{-- Admin Main Dashboard Skeleton --}}
    <div class="animate-pulse" id="adminDashboardSkeleton">
        <!-- Header Skeleton -->
        <div class="mb-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="h-10 w-80 bg-gray-200 rounded mb-2"></div>
                    <div class="h-5 w-96 bg-gray-100 rounded"></div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="bg-gray-200 rounded-lg px-8 py-4 w-56 h-10"></div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Skeleton -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-4 mb-3">
            @for ($i = 0; $i < 4; $i++)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-8 w-16 bg-gray-300 rounded"></div>
                        </div>
                        <div class="bg-gray-200 rounded-full w-10 h-10"></div>
                    </div>
                    <div class="h-4 w-20 bg-gray-100 rounded"></div>
                </div>
            @endfor
        </div>

        <!-- Charts Skeleton -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            @for ($i = 0; $i < 2; $i++)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex flex-col justify-between h-80">
                    <div class="h-6 w-2/5 bg-gray-200 rounded mb-6"></div>
                    <div class="flex items-center justify-center flex-1">
                        <div class="bg-gray-200 rounded-full w-40 h-40"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Trends Skeleton -->
        <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex flex-col h-56">
            <div class="h-6 w-2/5 bg-gray-200 rounded mb-6"></div>
            <div class="h-20 w-full bg-gray-200 rounded flex-1"></div>
        </div>

        <!-- FAB Skeleton -->
        <div class="fixed bottom-6 right-6 z-50 flex items-center justify-center">
            <div class="bg-gray-300 rounded-full w-16 h-16 shadow-lg flex items-center justify-center"></div>
        </div>
    </div>
@endif


