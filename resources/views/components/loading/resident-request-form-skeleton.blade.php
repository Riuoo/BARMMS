{{-- Resident Request Form Skeleton --}}
@php
    $variant = $variant ?? 'document'; // 'document' | 'blotter' | 'concern'
@endphp

<div id="residentRequestFormSkeleton" class="animate-pulse">
    <!-- Header Skeleton -->
    <div class="mb-2">
        <div class="h-8 {{ $variant === 'concern' ? 'w-96' : 'w-80' }} bg-gray-200 rounded"></div>
        <div class="h-4 {{ $variant === 'concern' ? 'w-[28rem]' : 'w-96' }} bg-gray-100 rounded"></div>
    </div>

    <!-- Form Skeleton -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-2">
        <div class="space-y-6">
            @if($variant === 'document')
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="border-b border-gray-200 pb-6 space-y-4">
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-56 bg-gray-200 rounded"></div>
                    <div class="h-16 w-full bg-gray-200 rounded"></div>
                </div>
            @elseif($variant === 'blotter')
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-60 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="border-b border-gray-200 pb-6 space-y-4">
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                    <div class="h-4 w-32 bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-44 bg-gray-200 rounded"></div>
                    <div class="h-24 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="h-32 w-full bg-gray-100 rounded border border-dashed border-gray-300"></div>
                </div>
            @elseif($variant === 'concern')
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-24 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="border-b border-gray-200 pb-6">
                    <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                    <div class="h-32 w-full bg-gray-100 rounded border border-dashed border-gray-300"></div>
                </div>
            @endif

            <div class="flex items-center justify-between pt-2">
                <div class="h-4 w-72 bg-gray-200 rounded"></div>
                <div class="flex space-x-3">
                    <div class="h-10 w-24 bg-gray-200 rounded"></div>
                    <div class="h-10 w-40 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>


