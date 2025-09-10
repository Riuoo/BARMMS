@extends('admin.main.layout')

@section('title', 'Add Adult Vaccination')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

    <!-- Header Skeleton -->
    <div id="createAdultHeaderSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'showButton' => false])
    </div>

    <!-- Form Skeleton -->
    <div id="createAdultFormSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'vaccination-adult'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createAdultContent" style="display: none;">
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add Adult Vaccination</h1>
                <p class="text-gray-600">Record a vaccination for an adult.</p>
            </div>
        </div>
    </div>

    @include('admin.vaccination-records._create-form', ['ageGroup' => 'adult'])
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('createAdultHeaderSkeleton');
        const formSkeleton = document.getElementById('createAdultFormSkeleton');
        const content = document.getElementById('createAdultContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection