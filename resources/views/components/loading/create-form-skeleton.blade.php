{{-- Consolidated Create Form Skeleton --}}
@php
    $type = $type ?? 'default'; // 'resident', 'barangay-profile', 'medicine', 'medicine-request', 'medical-record', 'vaccination-child', 'vaccination-adult', 'vaccination-elderly', 'child-profile', 'health-activity', 'blotter-report', 'document-request', 'accomplished-project', 'template', 'header', 'warning', 'default'
@endphp

@if($type === 'resident')
    {{-- Resident Form Skeleton --}}
    <div class="animate-pulse space-y-6" data-skeleton>
        <div class="h-8 w-48 bg-gray-200 rounded mx-auto mb-2 animate-pulse"></div>

        <!-- Basic Information Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <!-- Personal Information Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-28 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-4 w-28 bg-gray-200 rounded mb-1"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <!-- Demographic Information Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-52 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-20 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <div class="h-4 w-40 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <!-- Emergency Contact Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-40 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-36 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions Skeleton -->
        <div class="flex justify-between mt-8">
            <div class="h-10 w-24 bg-gray-200 rounded"></div>
            <div class="h-10 w-32 bg-gray-200 rounded"></div>
        </div>
    </div>

@elseif($type === 'barangay-profile')
    {{-- Barangay Profile Form Skeleton --}}
    <div class="animate-pulse space-y-6" data-skeleton>
        <!-- Page Title Skeleton -->
        <div class="h-8 w-64 bg-gray-200 rounded mx-auto mb-2"></div>
        
        <!-- Basic Information Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-40 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        <!-- Contact Information Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-44 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        <!-- Credentials Section Skeleton -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
            <div class="h-6 w-36 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        <!-- Form Actions Skeleton -->
        <div class="flex justify-between mt-8">
            <div class="h-10 w-24 bg-gray-200 rounded"></div>
            <div class="h-10 w-32 bg-gray-200 rounded"></div>
        </div>
    </div>

@elseif($type === 'medicine')
    {{-- Medicine Form Skeleton --}}
    <div class="animate-pulse bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6" data-skeleton>
        <!-- Medicine Information Section -->
        <div class="border-b border-gray-200 pb-6">
            <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @for($i = 0; $i < 4; $i++)
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Stock Information Section -->
        <div class="border-b border-gray-200 pb-6">
            <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @for($i = 0; $i < 3; $i++)
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between pt-6">
            <div class="h-10 w-20 bg-gray-200 rounded"></div>
            <div class="h-10 w-32 bg-gray-200 rounded"></div>
        </div>
    </div>

@elseif($type === 'medicine-request')
    {{-- Medicine Request Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <!-- Request Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-10 w-full bg-gray-200 rounded"></div>
                        <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Medical Record Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-3 w-40 bg-gray-100 rounded mt-2"></div>
                </div>
            </div>

            <!-- Medicine Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'medical-record')
    {{-- Medical Record Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <!-- Patient Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="h-10 w-full bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-48 bg-gray-100 rounded mb-2"></div>
                <div class="h-8 w-32 bg-gray-200 rounded"></div>
            </div>

            <!-- Consultation Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
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
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Diagnosis and Treatment Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-20 w-full bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-20 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif(in_array($type, ['vaccination-child', 'vaccination-adult', 'vaccination-elderly']))
    {{-- Vaccination Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <!-- Patient Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="h-10 w-full bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-48 bg-gray-100 rounded mb-2"></div>
                <div class="h-8 w-32 bg-gray-200 rounded"></div>
            </div>

            <!-- Vaccine Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Vaccination Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
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

@elseif($type === 'child-profile')
    {{-- Child Profile Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6" data-skeleton>
        <div class="space-y-6">
            <!-- Child Information Section -->
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
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

            <!-- Parent/Guardian Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-2">
                <div class="flex items-center mb-2">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
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

            <!-- Form Actions -->
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'health-activity')
    {{-- Health Activity Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Activity Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Schedule and Location Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
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

            <!-- Form Actions -->
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'blotter-report')
    {{-- Blotter Report Form Skeleton (matches Document layout width) --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Incident Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Parties Involved Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
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

@elseif($type === 'document-request')
    {{-- Document Request Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Request Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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

            <!-- Document Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
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

@elseif($type === 'accomplished-project')
    {{-- Accomplished Project Form Skeleton --}}
    <div class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Project Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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
                <div class="mt-4">
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-20 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Project Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-56 bg-gray-200 rounded"></div>
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

            <!-- Form Actions -->
            <div class="flex justify-between pt-6">
                <div class="h-10 w-20 bg-gray-200 rounded"></div>
                <div class="h-10 w-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

@elseif($type === 'template')
    {{-- Template Page Skeleton (Header + Form) --}}
    <div class="animate-pulse max-w-4xl mx-auto" data-skeleton>
        <!-- Header Skeleton -->
        <div class="mb-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="h-8 w-72 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 w-64 bg-gray-100 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Form Skeleton -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="space-y-6">
                <!-- Template Information Section -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center mb-4">
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

                <!-- Template Content Section -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                        <div class="h-6 w-56 bg-gray-200 rounded"></div>
                    </div>
                    <div>
                        <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                        <div class="h-32 w-full bg-gray-200 rounded"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-6">
                    <div class="h-10 w-20 bg-gray-200 rounded"></div>
                    <div class="h-10 w-32 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>

@elseif($type === 'header')
    {{-- Header Skeleton (configurable buttons) --}}
    <div class="mb-2 animate-pulse" data-skeleton>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <div class="h-10 w-80 bg-gray-200 rounded"></div>
                <div class="h-5 w-96 bg-gray-100 rounded"></div>
            </div>
            @if (!isset($showButton) || $showButton)
                @php $buttonCount = $buttonCount ?? 1; @endphp
                <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                    @for ($i = 0; $i < $buttonCount; $i++)
                        <div class="h-10 w-40 bg-gray-200 rounded"></div>
                    @endfor
                </div>
            @endif
        </div>
    </div>
@endif
