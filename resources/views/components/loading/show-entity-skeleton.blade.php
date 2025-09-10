{{-- Consolidated Show Skeleton --}}
@php
    $type = $type ?? 'default'; // 'vaccination', 'medical-record', 'health-activity', 'accomplished-project', 'default'
    // Show header by default only where we don't render a type-specific header at top
    $defaultShowHeader = in_array($type, ['vaccination', 'medical-record']);
    $effectiveShowHeader = array_key_exists('showHeader', get_defined_vars()) ? (bool)$showHeader : $defaultShowHeader;
@endphp

{{-- Optional Page Header (with configurable buttons) --}}
@if ($effectiveShowHeader)
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-8 w-64 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-96 bg-gray-100 rounded"></div>
            </div>
            @if (!isset($showButton) || $showButton)
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    @for ($i = 0; $i < $buttonCount; $i++)
                        <div class="h-10 w-32 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            @endif
        </div>
    </div>
@endif

@if($type === 'vaccination')
    <div class="animate-pulse">
        @php
            $sections = $sections ?? [];
            $showHeader = $sections['header'] ?? true;
            $showVaccineDetails = $sections['vaccineDetails'] ?? true;
            $showVaccinationDetails = $sections['vaccinationDetails'] ?? true;
            $showPatientInfo = $sections['patientInfo'] ?? true;
            $showRecordInfo = $sections['recordInfo'] ?? true;
        @endphp

        @if($showHeader)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mr-4"></div>
                    <div>
                        <div class="h-5 w-48 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-40 bg-gray-100 rounded"></div>
                    </div>
                </div>
                <div class="h-6 w-24 bg-gray-200 rounded"></div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @if($showVaccineDetails)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    @for($j=0;$j<3;$j++)
                    <div class="flex justify-between">
                        <div class="h-4 w-32 bg-gray-200 rounded"></div>
                        <div class="h-4 w-24 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endif
            @if($showVaccinationDetails)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    @for($j=0;$j<3;$j++)
                    <div class="flex justify-between">
                        <div class="h-4 w-32 bg-gray-200 rounded"></div>
                        <div class="h-4 w-24 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endif
        </div>

        @if($showPatientInfo)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                <div class="h-5 w-48 bg-gray-200 rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for($i=0;$i<3;$i++)
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-5 w-24 bg-gray-100 rounded"></div>
                </div>
                @endfor
            </div>
        </div>
        @endif

        @if($showRecordInfo)
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @for($i=0;$i<3;$i++)
                <div class="text-center">
                    <div class="w-8 h-8 bg-gray-200 rounded-full mx-auto mb-2"></div>
                    <div class="h-3 w-20 bg-gray-200 rounded mx-auto mb-1"></div>
                    <div class="h-4 w-24 bg-gray-100 rounded mx-auto"></div>
                </div>
                @endfor
            </div>
        </div>
        @endif
    </div>

@elseif($type === 'medical-record')
    <div class="animate-pulse">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mr-4"></div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                        <div class="h-5 w-48 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="h-6 w-24 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            @for($i=0;$i<2;$i++)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="h-5 w-48 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    @for($j=0;$j<4;$j++)
                    <div class="flex justify-between">
                        <div class="h-4 w-36 bg-gray-200 rounded"></div>
                        <div class="h-4 w-28 bg-gray-100 rounded"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endfor
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                <div class="h-5 w-48 bg-gray-200 rounded"></div>
            </div>
            <div class="space-y-2">
                @for($i=0;$i<3;$i++)
                <div class="h-4 w-full bg-gray-100 rounded"></div>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                <div class="h-5 w-40 bg-gray-200 rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @for($i=0;$i<4;$i++)
                <div>
                    <div class="h-3 w-28 bg-gray-200 rounded mb-2"></div>
                    <div class="h-5 w-24 bg-gray-100 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                    <div class="h-5 w-32 bg-gray-200 rounded"></div>
                </div>
                <div class="space-y-2">
                    @for($i=0;$i<3;$i++)
                    <div class="h-4 w-full bg-gray-100 rounded"></div>
                    @endfor
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                    <div class="h-5 w-32 bg-gray-200 rounded"></div>
                </div>
                <div class="space-y-2">
                    @for($i=0;$i<3;$i++)
                    <div class="h-4 w-full bg-gray-100 rounded"></div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-5 h-5 bg-gray-100 rounded mr-2"></div>
                <div class="h-5 w-48 bg-gray-200 rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @for($i=0;$i<2;$i++)
                <div>
                    <div class="h-3 w-36 bg-gray-200 rounded mb-2"></div>
                    <div class="h-5 w-40 bg-gray-100 rounded"></div>
                </div>
                @endfor
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @for($i=0;$i<3;$i++)
                <div class="text-center">
                    <div class="w-8 h-8 bg-gray-200 rounded-full mx-auto mb-2"></div>
                    <div class="h-3 w-20 bg-gray-200 rounded mx-auto mb-1"></div>
                    <div class="h-4 w-24 bg-gray-100 rounded mx-auto"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

@elseif($type === 'health-activity')
    <div class="animate-pulse">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="h-8 w-64 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-96 bg-gray-100 rounded"></div>
                </div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden h-64"></div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        @for($i=0;$i<3;$i++)
                        <div class="h-4 w-full bg-gray-100 rounded"></div>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-32 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        @for($i=0;$i<3;$i++)
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'accomplished-project')
    <div class="animate-pulse">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="h-8 w-64 bg-gray-200 rounded"></div>
                <div class="h-8 w-28 bg-gray-200 rounded"></div>
            </div>
            <div class="h-4 w-96 bg-gray-100 rounded mt-2"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden h-64"></div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-40 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        @for($i=0;$i<4;$i++)
                        <div class="h-4 w-full bg-gray-100 rounded"></div>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6 h-40"></div>
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="h-5 w-32 bg-gray-200 rounded mb-4"></div>
                    <div class="space-y-3">
                        @for($i=0;$i<3;$i++)
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

@else
    <div class="animate-pulse bg-white rounded-lg shadow p-6">
        <div class="h-6 w-64 bg-gray-200 rounded mb-4"></div>
        <div class="space-y-3">
            @for($i=0;$i<3;$i++)
            <div class="h-4 w-full bg-gray-100 rounded"></div>
            @endfor
        </div>
    </div>
@endif


