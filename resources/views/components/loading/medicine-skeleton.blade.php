{{-- Skeleton loader for Medicine Dispense Report --}}
@php
    // You can pass extra options if needed, e.g.:
    // $type = $type ?? 'report';
@endphp

<div id="medicineReportSkeletonWrapper" class="animate-pulse" data-skeleton>
    {{-- Header skeleton --}}
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-8 w-64 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-80 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <div class="h-9 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    {{-- Filter form skeleton --}}
    <div class="mb-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            @for ($i = 0; $i < 2; $i++)
                <div>
                    <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                    <div class="h-9 w-full bg-gray-200 rounded"></div>
                </div>
            @endfor
            <div class="flex space-x-2">
                <div class="h-9 w-20 bg-gray-200 rounded"></div>
                <div class="h-9 w-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    {{-- Charts grid skeleton --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @for ($i = 0; $i < 2; $i++)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-4 w-40 bg-gray-200 rounded"></div>
                    <div class="h-3 w-10 bg-gray-100 rounded"></div>
                </div>
                <div class="h-48 bg-gray-100 rounded"></div>
            </div>
        @endfor
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        @for ($i = 0; $i < 3; $i++)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-4 w-32 bg-gray-200 rounded"></div>
                    <div class="h-3 w-12 bg-gray-100 rounded"></div>
                </div>
                <div class="h-40 bg-gray-100 rounded"></div>
            </div>
        @endfor
    </div>

    {{-- Category distribution + info box skeleton --}}
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="h-4 w-40 bg-gray-200 rounded mb-3"></div>
            <div class="h-40 bg-gray-100 rounded"></div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-blue-100 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 w-32 bg-blue-100 rounded"></div>
                @for ($i = 0; $i < 4; $i++)
                    <div class="h-3 w-full bg-blue-100 rounded"></div>
                @endfor
            </div>
        </div>
    </div>
</div>


