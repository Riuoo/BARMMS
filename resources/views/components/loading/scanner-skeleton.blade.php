{{-- QR Scanner Page Skeleton --}}
<div class="animate-pulse" data-skeleton>
    <!-- Header Skeleton -->
    <div class="mb-6">
        <div class="h-9 w-64 bg-gray-200 rounded mb-2"></div>
        <div class="h-5 w-80 bg-gray-100 rounded"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scanner Section Skeleton -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                
                <!-- Event Selection Skeleton -->
                <div class="mb-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div class="h-3 w-72 bg-gray-100 rounded mt-1"></div>
                </div>

                <!-- Camera/Video Skeleton -->
                <div class="mb-4">
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 400px;">
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-300 rounded-full mx-auto mb-2"></div>
                                <div class="h-4 w-32 bg-gray-300 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <div class="h-10 w-32 bg-gray-200 rounded"></div>
                    </div>
                </div>

                <!-- Manual Input Skeleton -->
                <div class="mb-4">
                    <div class="h-4 w-48 bg-gray-200 rounded mb-2"></div>
                    <div class="flex gap-2">
                        <div class="flex-1 h-10 bg-gray-200 rounded"></div>
                        <div class="h-10 w-24 bg-gray-200 rounded"></div>
                    </div>
                </div>

                <!-- Guest Attendance Skeleton -->
                <div class="mb-4 border-t border-gray-200 pt-4">
                    <div class="h-5 w-48 bg-gray-200 rounded mb-3"></div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-3">
                        <div class="h-3 w-full bg-purple-200 rounded"></div>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <div class="h-3 w-20 bg-gray-200 rounded mb-1"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-3 w-32 bg-gray-200 rounded mb-1"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Skeleton -->
        <div class="lg:col-span-1">
            <!-- Attendance Info Skeleton -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="text-center mb-4">
                    <div class="h-12 w-24 bg-gray-200 rounded mx-auto mb-2"></div>
                    <div class="h-4 w-32 bg-gray-100 rounded mx-auto"></div>
                </div>
                <div class="space-y-2">
                    @for($i = 0; $i < 3; $i++)
                    <div class="bg-gray-50 rounded p-2">
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-3 w-24 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Quick Actions Skeleton -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="h-6 w-32 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-2">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

