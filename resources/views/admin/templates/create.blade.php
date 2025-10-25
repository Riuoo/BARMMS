@extends('admin.main.layout')

@section('title', 'Create New Template')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Page Skeleton (Header + Form) -->
    <div id="createTemplateSkeleton" class="mb-2">
        @include('components.loading.create-form-skeleton', ['type' => 'template'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createTemplateContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Template</h1>
                    <p class="text-gray-600">Upload a Microsoft Word document to create your template</p>
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

    <!-- DOCX Upload Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Document Template</h2>
            
            <!-- Validation/Error Feedback -->
            @if(session('error') || (isset(
                $errors) && $errors->any()))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @if(session('error'))
                        <div>{{ session('error') }}</div>
                    @endif
                </div>
            @endif

            <form action="{{ route('admin.templates.store-from-docx') }}" method="POST" enctype="multipart/form-data" id="docxUploadForm">
            @csrf
                
                <div class="space-y-6">
                    <!-- Document Type -->
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Document Type *
                        </label>
                        <select id="document_type" name="document_type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select document type...</option>
                            <option value="Barangay Clearance">Barangay Clearance</option>
                            <option value="Certificate of Residency">Certificate of Residency</option>
                            <option value="Certificate of Indigency">Certificate of Indigency</option>
                            <option value="Certificate of Good Moral Character">Certificate of Good Moral Character</option>
                            <option value="Certificate of Live Birth">Certificate of Live Birth</option>
                            <option value="Certificate of Death">Certificate of Death</option>
                            <option value="Certificate of Marriage">Certificate of Marriage</option>
                            <option value="Barangay ID">Barangay ID</option>
                            <option value="Certificate of No Pending Case">Certificate of No Pending Case</option>
                            <option value="Certificate of No Derogatory Record">Certificate of No Derogatory Record</option>
                            <option value="Certificate of First Time Job Seeker">Certificate of First Time Job Seeker</option>
                            <option value="Certificate of Solo Parent">Certificate of Solo Parent</option>
                            <option value="Certificate of Senior Citizen">Certificate of Senior Citizen</option>
                            <option value="Certificate of PWD (Person with Disability)">Certificate of PWD (Person with Disability)</option>
                            <option value="Certificate of Tribal Membership">Certificate of Tribal Membership</option>
                            <option value="Certificate of Land Ownership">Certificate of Land Ownership</option>
                            <option value="Certificate of Business Operation">Certificate of Business Operation</option>
                            <option value="Certificate of Community Tax Certificate">Certificate of Community Tax Certificate</option>
                            <option value="Certificate of No Property">Certificate of No Property</option>
                            <option value="Certificate of Low Income">Certificate of Low Income</option>
                            <option value="Other">Other (Custom)</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose from common document types or select "Other" for custom types</p>
                    </div>

                    <!-- Custom Document Type (shown when "Other" is selected) -->
                    <div id="custom_document_type_div" class="hidden">
                        <label for="custom_document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Custom Document Type *
                </label>
                        <input type="text" id="custom_document_type" name="custom_document_type" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               placeholder="e.g., Certificate of Employment, Special Permit">
                        <p class="mt-1 text-sm text-gray-500">Enter the name of your custom document type</p>
            </div>

                    <!-- DOCX File Upload -->
                    <div>
                        <label for="docx_file" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-word mr-2"></i>Upload DOCX File *
                        </label>
                        <input id="docx_file" name="docx_file" type="file" accept=".docx" required>
                        <p class="mt-1 text-sm text-gray-500">Upload your Microsoft Word document (.docx) to create the template</p>
                        <p class="text-xs text-gray-500">DOCX files only, up to 10MB</p>
                    </div>

                    <!-- Instructions -->
                        <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Instructions
                            </h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Create your document in Microsoft Word</li>
                            <li>• Use placeholders like <code class="bg-blue-100 px-1 rounded">[resident_name]</code> for dynamic content</li>
                            <li>• Save as .docx format (not .doc)</li>
                            <li>• The system will convert it to HTML for web display</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-upload mr-2"></i>
                    Create Template
                </button>
                </div>
            </form>
            </div>
    </div>
    </div>
</div>

@endsection 