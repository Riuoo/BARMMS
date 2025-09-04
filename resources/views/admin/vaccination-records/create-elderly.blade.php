@extends('admin.main.layout')

@section('title', 'Add Elderly Vaccination')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Skeleton -->
    <div id="createElderlyHeaderSkeleton" class="animate-pulse mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 w-72 bg-gray-100 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Form Skeleton -->
    <div id="createElderlyFormSkeleton" class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- Patient Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="h-10 w-full bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-48 bg-gray-100 rounded"></div>
            </div>

            <!-- Vaccine Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-6 w-48 bg-gray-200 rounded"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- Vaccination Details Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
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

    <!-- Real Content (hidden initially) -->
    <div id="createElderlyContent" style="display: none;">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Add Elderly Vaccination</h1>
                <p class="text-gray-600">Record a vaccination for an elderly resident.</p>
            </div>
        </div>
    </div>

    @include('admin.vaccination-records._create-form', ['ageGroup' => 'elderly'])
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('createElderlyHeaderSkeleton');
        const formSkeleton = document.getElementById('createElderlyFormSkeleton');
        const content = document.getElementById('createElderlyContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection
