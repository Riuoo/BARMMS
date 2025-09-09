@extends('admin.main.layout')

@section('title', 'Add Elderly Vaccination')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Skeleton -->
    <div id="createElderlyHeaderSkeleton">
        @include('components.loading.skeleton-vaccination-create-header')
    </div>

    <!-- Form Skeleton -->
    <div id="createElderlyFormSkeleton">
        @include('components.loading.skeleton-vaccination-form')
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