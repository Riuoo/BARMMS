@extends('admin.main.layout')

@section('title', 'Edit Template - ' . $template->template_type)

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Template</h1>
                <p class="text-gray-600">{{ $template->template_type }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.blotter-templates.index') }}" 
                   class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <form action="{{ route('admin.blotter-templates.update', $template) }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf
        @method('PUT')

        <!-- Header Content -->
        <div class="mb-6">
            <label for="header_content" class="block text-sm font-medium text-gray-700 mb-2">
                Header Content
            </label>
            <textarea 
                id="header_content" 
                name="header_content" 
                rows="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                placeholder="Enter header content HTML..."
            >{{ old('header_content', $template->header_content) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">HTML content for the header section. Use placeholders like [case_id], [complainant_name], etc.</p>
        </div>

        <!-- Body Content -->
        <div class="mb-6">
            <label for="body_content" class="block text-sm font-medium text-gray-700 mb-2">
                Body Content <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="body_content" 
                name="body_content" 
                rows="12"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                placeholder="Enter body content HTML..."
            >{{ old('body_content', $template->body_content) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">HTML content for the body section. This is the main content area.</p>
        </div>

        <!-- Footer Content -->
        <div class="mb-6">
            <label for="footer_content" class="block text-sm font-medium text-gray-700 mb-2">
                Footer Content
            </label>
            <textarea 
                id="footer_content" 
                name="footer_content" 
                rows="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                placeholder="Enter footer content HTML..."
            >{{ old('footer_content', $template->footer_content) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">HTML content for the footer section (signatures, etc.).</p>
        </div>

        <!-- Custom CSS -->
        <div class="mb-6">
            <label for="custom_css" class="block text-sm font-medium text-gray-700 mb-2">
                Custom CSS (Optional)
            </label>
            <textarea 
                id="custom_css" 
                name="custom_css" 
                rows="6"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                placeholder="Enter custom CSS..."
            >{{ old('custom_css', $template->custom_css) }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Additional CSS styles for the template.</p>
        </div>

        <!-- Available Placeholders -->
        <div class="mb-6 bg-blue-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Available Placeholders</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                @foreach($template->getValidPlaceholders() as $key => $placeholder)
                    <div class="flex items-center">
                        <code class="bg-blue-100 px-2 py-1 rounded text-blue-800">[{{ $key }}]</code>
                        <span class="ml-2 text-blue-700 text-xs">{{ $placeholder['label'] ?? $key }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.blotter-templates.preview', $template) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-eye mr-2"></i>
                Preview
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

