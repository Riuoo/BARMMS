{{-- Events Index Page Skeleton --}}
<div class="animate-pulse" data-skeleton>
    <!-- Header Skeleton -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-9 w-64 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-96 bg-gray-100 rounded mb-1"></div>
                <div class="h-4 w-80 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="h-10 w-36 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Table Skeleton -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">
                            <div class="h-4 w-24 bg-gray-200 rounded"></div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="h-4 w-16 bg-gray-200 rounded"></div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="h-4 w-16 bg-gray-200 rounded"></div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="h-4 w-16 bg-gray-200 rounded"></div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="h-4 w-20 bg-gray-200 rounded"></div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 0; $i < 6; $i++)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="h-4 w-40 bg-gray-200 rounded"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 w-28 bg-gray-200 rounded"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 w-24 bg-gray-200 rounded"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 w-16 bg-gray-200 rounded"></div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <!-- Pagination Skeleton -->
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="h-4 w-48 bg-gray-200 rounded"></div>
                <div class="flex gap-2">
                    @for($i = 0; $i < 5; $i++)
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

