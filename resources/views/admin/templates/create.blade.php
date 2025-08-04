@extends('admin.main.layout')

@section('title', 'Create New Template')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Template</h1>
                <p class="text-gray-600">Add a new document template</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.templates.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Document Type
                </label>
                <input type="text" 
                       id="document_type" 
                       name="document_type" 
                       value="{{ old('document_type') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                       placeholder="e.g., Barangay Clearance, Certificate of Residency"
                       required>
                @error('document_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                          placeholder="Brief description of this document type"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 