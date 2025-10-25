@extends('admin.main.layout')

@section('title', 'Edit Template - ' . $template->document_type)

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Template</h1>
                <p class="text-gray-600">{{ $template->document_type }} - Download, edit, and re-upload</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.templates.index') }}" 
                   class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <!-- DOCX Workflow -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Edit with Microsoft Word</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Download Section -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-download text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-blue-900">Step 1: Download</h3>
                            <p class="text-sm text-blue-700">Get the current template as a Word document</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <p class="text-sm text-blue-800">
                            Download the template as a Microsoft Word document (.docx) to edit it with all the formatting and features you need.
                        </p>
                        
                        <a href="{{ route('admin.templates.download-docx', $template) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Download as DOCX
                        </a>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-upload text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-green-900">Step 2: Upload</h3>
                            <p class="text-sm text-green-700">Upload your edited document</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.templates.upload-docx', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="docx_file" class="block text-sm font-medium text-green-700 mb-2">
                                Upload Edited DOCX File
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-green-300 border-dashed rounded-md hover:border-green-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-green-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4l.01.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-green-600">
                                        <label for="docx_file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>Upload a file</span>
                                            <input id="docx_file" name="docx_file" type="file" accept=".docx" required class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-green-500">DOCX files only, up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                            <i class="fas fa-upload mr-2"></i>
                            Upload & Update Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Information -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Current Placeholders</h4>
                    <div class="space-y-2">
                        @if($template->placeholders && count($template->placeholders) > 0)
                            @foreach($template->placeholders as $placeholder)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div>
                                        <code class="text-sm font-mono text-blue-600">[{{ $placeholder }}]</code>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No placeholders found</p>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Template Preview</h4>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="text-sm text-gray-600 mb-2">Last updated: {{ $template->updated_at->format('M d, Y H:i') }}</div>
                        <a href="{{ route('admin.templates.preview', $template) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-8 bg-blue-50 rounded-lg border border-blue-200 p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>
            Instructions for Word Document Editing
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Using Placeholders</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Use <code class="bg-blue-100 px-1 rounded">[resident_name]</code> for resident names</li>
                    <li>• Use <code class="bg-blue-100 px-1 rounded">[current_date]</code> for current date</li>
                    <li>• Use <code class="bg-blue-100 px-1 rounded">[barangay_name]</code> for barangay name</li>
                    <li>• Don't change placeholder names - they must match exactly</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Word Editing Tips</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Edit the document normally in Microsoft Word</li>
                    <li>• Add placeholders using the exact format: [placeholder_name]</li>
                    <li>• Save as .docx format (not .doc)</li>
                    <li>• Upload the edited document to update the template</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection