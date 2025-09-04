@extends('resident.layout')

@section('title', 'Request New Document')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Skeleton -->
    <div id="rdHeaderSkeleton" class="animate-pulse mb-8">
        <div class="h-8 w-80 bg-gray-200 rounded mb-2"></div>
        <div class="h-4 w-96 bg-gray-100 rounded"></div>
    </div>

    <!-- Form Skeleton -->
    <div id="rdFormSkeleton" class="animate-pulse bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <div class="h-6 w-56 bg-gray-200 rounded mb-4"></div>
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
            <div class="flex items-center justify-between pt-2">
                <div class="h-4 w-72 bg-gray-200 rounded"></div>
                <div class="flex space-x-3">
                    <div class="h-10 w-24 bg-gray-200 rounded"></div>
                    <div class="h-10 w-40 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="rdContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Request New Document</h1>
                <p class="text-gray-600">Request official documents from the barangay office</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('resident.request_document_request') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Document Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-file-signature mr-2 text-blue-600"></i>
                    Document Information
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                id="document_type" 
                                name="document_type" 
                                required>
                            <option value="">Select a document type</option>
                            @foreach(($templates ?? []) as $t)
                                <option value="{{ optional($t)->document_type ?? '' }}" {{ old('document_type') == (optional($t)->document_type ?? '') ? 'selected' : '' }}>
                                    {{ optional($t)->document_type ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the type of document you need</p>
                    </div>
                </div>
            </div>

            <!-- Purpose and Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-align-left mr-2 text-blue-600"></i>
                    Purpose and Details
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Please specify the purpose of your document request (e.g., For job application, school enrollment, financial assistance, government transaction, etc.)"
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Provide a clear and specific purpose for your document request</p>
                    </div>

                    <div>
                        <label for="additional_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Requirements (Optional)
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                  id="additional_requirements" 
                                  name="additional_requirements" 
                                  rows="3" 
                                  placeholder="Mention any specific requirements or special instructions for your document request">{{ old('additional_requirements') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Include any special requirements or additional information needed</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your request will be processed by barangay officials
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('resident.my-requests') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Requirements Information -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Requirements</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Additional requirements may vary by document type</li>
                        <li>Processing fees may apply depending on the document</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const hs = document.getElementById('rdHeaderSkeleton');
        const fs = document.getElementById('rdFormSkeleton');
        const content = document.getElementById('rdContent');
        if (hs) hs.style.display = 'none';
        if (fs) fs.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush