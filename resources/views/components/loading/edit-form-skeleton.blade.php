{{-- Consolidated Edit Form Skeleton --}}
@php
    $type = $type ?? 'default'; // 'resident', 'barangay-profile', 'medicine', 'medical-record', 'vaccination-edit', 'health-activity', 'accomplished-project', 'template', 'header', 'default'
@endphp

@if($type === 'header')
    {{-- Generic Edit Header Skeleton --}}
    <div class="mb-2 animate-pulse" data-skeleton>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-10 w-80 bg-gray-200 rounded"></div>
                <div class="h-5 w-64 bg-gray-100 rounded"></div>
            </div>
            @if (!isset($showButton) || $showButton)
                @php $buttonCount = $buttonCount ?? 2; @endphp
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    @for ($i = 0; $i < $buttonCount; $i++)
                        <div class="h-10 w-28 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            @endif
        </div>
    </div>

@elseif($type === 'resident' || $type === 'barangay-profile')
    {{-- Resident / Barangay Profile Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i=0;$i<4;$i++)
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-40 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i=0;$i<2;$i++)
                        <div>
                            <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'medicine')
    {{-- Medicine Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-56 bg-gray-200 rounded mb-2"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i = 0; $i < 4; $i++)
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'vaccination-edit')
    {{-- Vaccination Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            {{-- Vaccine Details --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i=0;$i<4;$i++)
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Dose & Schedule --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-2"></div>
                    <div class="h-6 w-40 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @for($i=0;$i<3;$i++)
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                <div class="h-24 w-full bg-gray-200 rounded"></div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="h-4 w-64 bg-gray-200 rounded"></div>
                <div class="flex space-x-3">
                    <div class="h-10 w-24 bg-gray-200 rounded"></div>
                    <div class="h-10 w-48 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'health-activity')
    {{-- Health Activity Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            {{-- Activity Header & Basic Details --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            {{-- Schedule --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-40 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            {{-- Venue --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-32 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-36 bg-gray-200 rounded"></div>
                </div>
                <div class="h-24 w-full bg-gray-200 rounded"></div>
            </div>

            {{-- Participants / Target Audience --}}
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
                </div>
                <div class="h-20 w-full bg-gray-200 rounded"></div>
            </div>

            {{-- Footer Actions --}}
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'accomplished-project')
    {{-- Accomplished Project Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>

            <div class="border-2 border-dashed border-gray-300 rounded-lg h-40"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <div>
                <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                <div class="h-24 w-full bg-gray-200 rounded"></div>
            </div>
            <div>
                <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                <div class="h-20 w-full bg-gray-200 rounded"></div>
            </div>
            <div>
                <div class="h-4 w-36 bg-gray-200 rounded mb-2"></div>
                <div class="h-20 w-full bg-gray-200 rounded"></div>
            </div>

            <div class="flex items-center pt-2">
                <div class="w-5 h-5 bg-gray-200 rounded mr-2"></div>
                <div class="h-4 w-64 bg-gray-200 rounded"></div>
            </div>

            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'template')
    {{-- Template Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@else
    {{-- Default Edit Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-40 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>
@endif




