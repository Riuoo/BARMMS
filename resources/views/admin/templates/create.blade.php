@extends('admin.main.layout')

@section('title', 'Create New Template')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Skeleton -->
    <div id="createTemplateHeaderSkeleton" class="mb-8 animate-pulse">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="h-8 w-80 bg-gray-200 rounded mb-2"></div>
                <div class="h-5 w-96 bg-gray-100 rounded"></div>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="h-10 w-40 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Form Skeleton -->
    <div id="createTemplateFormSkeleton" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden animate-pulse">
        <!-- Wizard Progress Skeleton -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="h-4 w-24 bg-gray-200 rounded ml-2"></div>
                    </div>
                    <div class="w-8 h-1 bg-gray-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="h-4 w-32 bg-gray-200 rounded ml-2"></div>
                    </div>
                    <div class="w-8 h-1 bg-gray-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        <div class="h-4 w-28 bg-gray-200 rounded ml-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Content Skeleton -->
        <div class="p-6">
            <div class="h-6 w-48 bg-gray-200 rounded mb-4"></div>
            <div class="space-y-6">
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-64 bg-gray-100 rounded mt-1"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-32 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-64 bg-gray-100 rounded mt-1"></div>
                </div>
                <div>
                    <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 w-full bg-gray-200 rounded"></div>
                    <div class="h-4 w-64 bg-gray-100 rounded mt-1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createTemplateContent" style="display: none;">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Template</h1>
                    <p class="text-gray-600">Use the wizard to create a new document template</p>
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

    <!-- Template Creation Wizard -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Wizard Progress -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium" id="step1-indicator">1</div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Basic Info</span>
                    </div>
                    <div class="w-8 h-1 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium" id="step2-indicator">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Template Content</span>
                    </div>
                    <div class="w-8 h-1 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium" id="step3-indicator">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Review & Save</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.templates.store') }}" method="POST" id="templateWizardForm">
            @csrf
            
            <!-- Step 1: Basic Information -->
            <div id="step1" class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 1: Basic Information</h2>
                
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

                    <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                        <textarea id="description" name="description" rows="3"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Brief description of what this document is used for"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Optional: Describe the purpose and use of this document</p>
                    </div>

                    <!-- Template Category -->
                    <div>
                        <label for="template_category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <select id="template_category" name="template_category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="certificates">Certificates</option>
                            <option value="clearances">Clearances</option>
                            <option value="permits">Permits</option>
                            <option value="identifications">Identifications</option>
                            <option value="reports">Reports</option>
                            <option value="other">Other</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Categorize your template for better organization</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" id="nextStep1" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        Next Step
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Template Content -->
            <div id="step2" class="p-6 hidden">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 2: Template Content</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Panel: Content Editor -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Template Preset -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quick Start Template
                            </label>
                            <select id="template_preset" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Choose a template preset...</option>
                                <option value="official_letter">Official Letter Format</option>
                                <option value="certificate">Certificate Format</option>
                                <option value="clearance">Clearance Format</option>
                                <option value="permit">Permit Format</option>
                                <option value="blank">Start from scratch</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select a preset to get started quickly</p>
                        </div>

                        <!-- Header Content -->
                        <div>
                            <label for="header_content" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-heading mr-2"></i>Header Content
                            </label>
                            <textarea id="header_content" name="header_content" class="tinymce-basic"></textarea>
                            <p class="mt-1 text-sm text-gray-500">This appears at the top of the document (e.g., official letterhead, title)</p>
                        </div>

                        <!-- Body Content -->
                        <div>
                            <label for="body_content" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2"></i>Body Content
                            </label>
                            <textarea id="body_content" name="body_content" class="tinymce-basic"></textarea>
                            <p class="mt-1 text-sm text-gray-500">This is the main content of the document</p>
                        </div>

                        <!-- Footer Content -->
                        <div>
                            <label for="footer_content" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-signature mr-2"></i>Footer Content
                            </label>
                            <textarea id="footer_content" name="footer_content" class="tinymce-basic"></textarea>
                            <p class="mt-1 text-sm text-gray-500">This appears at the bottom (e.g., signature line, official details)</p>
                        </div>
                    </div>

                    <!-- Right Panel: Tools -->
                    <div class="space-y-6">
                        <!-- Placeholder Toolbox -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">
                                <i class="fas fa-tags mr-2"></i>Insert Placeholders
                            </h3>
                            <div class="space-y-2">
                                <button type="button" class="placeholder-btn w-full text-left p-2 rounded border border-gray-200 hover:bg-white transition-colors" data-placeholder="resident_name">
                                    <code class="text-sm font-mono text-green-600">[resident_name]</code>
                                    <p class="text-xs text-gray-600">Resident's full name</p>
                                </button>
                                <button type="button" class="placeholder-btn w-full text-left p-2 rounded border border-gray-200 hover:bg-white transition-colors" data-placeholder="resident_address">
                                    <code class="text-sm font-mono text-green-600">[resident_address]</code>
                                    <p class="text-xs text-gray-600">Resident's address</p>
                                </button>
                                <button type="button" class="placeholder-btn w-full text-left p-2 rounded border border-gray-200 hover:bg-white transition-colors" data-placeholder="current_date">
                                    <code class="text-sm font-mono text-green-600">[current_date]</code>
                                    <p class="text-xs text-gray-600">Current date</p>
                                </button>
                                <button type="button" class="placeholder-btn w-full text-left p-2 rounded border border-gray-200 hover:bg-white transition-colors" data-placeholder="barangay_name">
                                    <code class="text-sm font-mono text-green-600">[barangay_name]</code>
                                    <p class="text-xs text-gray-600">Barangay name</p>
                                </button>
                                <button type="button" class="placeholder-btn w-full text-left p-2 rounded border border-gray-200 hover:bg-white transition-colors" data-placeholder="official_name">
                                    <code class="text-sm font-mono text-green-600">[official_name]</code>
                                    <p class="text-xs text-gray-600">Official's name</p>
                                </button>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-blue-900 mb-3">
                                <i class="fas fa-magic mr-2"></i>Quick Actions
                            </h3>
                            <div class="space-y-2">
                                <button type="button" id="insertHeaderBtn" class="w-full text-left p-2 rounded border border-blue-200 hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-heading mr-2 text-blue-600"></i>
                                    <span class="text-sm font-medium">Insert Header</span>
                                </button>
                                <button type="button" id="insertSignatureBtn" class="w-full text-left p-2 rounded border border-blue-200 hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-signature mr-2 text-blue-600"></i>
                                    <span class="text-sm font-medium">Insert Signature</span>
                                </button>
                                <button type="button" id="insertDateBtn" class="w-full text-left p-2 rounded border border-blue-200 hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                    <span class="text-sm font-medium">Insert Date</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" id="prevStep2" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous Step
                    </button>
                    <button type="button" id="nextStep2" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        Next Step
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Review & Save -->
            <div id="step3" class="p-6 hidden">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 3: Review & Save</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Template Summary -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Template Summary</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Document Type:</span>
                                <p id="summary_document_type" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Category:</span>
                                <p id="summary_category" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Description:</span>
                                <p id="summary_description" class="text-sm text-gray-900"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Document Preview</h3>
                        <div id="finalPreview" class="border border-gray-200 rounded p-4 bg-white" style="min-height: 300px; font-family: 'Times New Roman', serif;">
                            <!-- Preview content will be loaded here -->
                        </div>
                    </div>
            </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" id="prevStep3" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous Step
                    </button>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Create Template
                </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

@endsection 

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// Skeleton loading control
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('createTemplateHeaderSkeleton');
        const formSkeleton = document.getElementById('createTemplateFormSkeleton');
        const content = document.getElementById('createTemplateContent');
        
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});

document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;

    // Initialize TinyMCE for basic editing
    tinymce.init({
        selector: '.tinymce-basic',
        height: 200,
        plugins: ['advlist', 'autolink', 'lists', 'link', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount'],
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | help',
        menubar: 'file edit view insert format tools table help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false
    });

    // Step navigation
    function showStep(step) {
        // Hide all steps
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById(`step${i}`).classList.add('hidden');
            document.getElementById(`step${i}-indicator`).classList.remove('bg-green-600', 'text-white');
            document.getElementById(`step${i}-indicator`).classList.add('bg-gray-300', 'text-gray-600');
        }

        // Show current step
        document.getElementById(`step${step}`).classList.remove('hidden');
        document.getElementById(`step${step}-indicator`).classList.remove('bg-gray-300', 'text-gray-600');
        document.getElementById(`step${step}-indicator`).classList.add('bg-green-600', 'text-white');

        currentStep = step;
    }

    // Next step buttons
    document.getElementById('nextStep1').addEventListener('click', function() {
        if (validateStep1()) {
            showStep(2);
        }
    });

    document.getElementById('nextStep2').addEventListener('click', function() {
        if (validateStep2()) {
            updateSummary();
            updatePreview();
            showStep(3);
        }
    });

    // Previous step buttons
    document.getElementById('prevStep2').addEventListener('click', function() {
        showStep(1);
    });

    document.getElementById('prevStep3').addEventListener('click', function() {
        showStep(2);
    });

    // Validation functions
    function validateStep1() {
        const documentType = document.getElementById('document_type').value;
        if (!documentType) {
            alert('Please select a document type');
            return false;
        }
        if (documentType === 'Other') {
            const customType = document.getElementById('custom_document_type').value;
            if (!customType) {
                alert('Please enter a custom document type');
                return false;
            }
        }
        return true;
    }

    function validateStep2() {
        const bodyContent = tinymce.get('body_content').getContent();
        if (!bodyContent || bodyContent.trim() === '') {
            alert('Please add some content to the document body');
            return false;
        }
        return true;
    }

    // Custom document type handling
    document.getElementById('document_type').addEventListener('change', function() {
        const customDiv = document.getElementById('custom_document_type_div');
        if (this.value === 'Other') {
            customDiv.classList.remove('hidden');
        } else {
            customDiv.classList.add('hidden');
        }
    });

    // Template preset handling
    document.getElementById('template_preset').addEventListener('change', function() {
        const preset = this.value;
        if (preset === 'official_letter') {
            tinymce.get('header_content').setContent(`
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">REPUBLIC OF THE PHILIPPINES</h1>
                    <h2 style="font-size: 16px; margin-bottom: 5px;">Province of [province_name]</h2>
                    <h2 style="font-size: 16px; margin-bottom: 5px;">Municipality of [municipality_name]</h2>
                    <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">BARANGAY [barangay_name]</h1>
                </div>
            `);
            tinymce.get('body_content').setContent(`
                <p>TO WHOM IT MAY CONCERN:</p>
                <p>This is to certify that [resident_name], of legal age, Filipino, and a resident of [resident_address], Barangay [barangay_name], [municipality_name], [province_name], is known to me as a person of good moral character and law-abiding citizen of this Barangay.</p>
                <p>This certification is being issued upon the request of the above-named person for [document_purpose].</p>
            `);
            tinymce.get('footer_content').setContent(`
                <p>Issued this [current_date] at the Office of the Barangay Chairman, Barangay [barangay_name], [municipality_name], [province_name].</p>
                <div style="margin-top: 50px; text-align: right;">
                    <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; margin-bottom: 10px;"></div>
                    <p style="font-weight: bold; margin-bottom: 5px;">[official_name]</p>
                    <p style="font-size: 14px; color: #666;">[official_position]</p>
                </div>
            `);
        } else if (preset === 'certificate') {
            tinymce.get('header_content').setContent(`
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">CERTIFICATE</h1>
                    <h2 style="font-size: 16px; margin-bottom: 5px;">Barangay [barangay_name]</h2>
                    <h2 style="font-size: 16px; margin-bottom: 5px;">[municipality_name], [province_name]</h2>
                </div>
            `);
            tinymce.get('body_content').setContent(`
                <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], has no pending case/s or record on file at the Office of the Barangay.</p>
                <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
            `);
            tinymce.get('footer_content').setContent(`
                <p>Issued this [current_date] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                <div style="margin-top: 50px; text-align: right;">
                    <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; margin-bottom: 10px;"></div>
                    <p style="font-weight: bold; margin-bottom: 5px;">[official_name]</p>
                    <p style="font-size: 14px; color: #666;">[official_position]</p>
                </div>
            `);
        }
    });

    // Placeholder insertion
    document.querySelectorAll('.placeholder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const placeholder = this.dataset.placeholder;
            const activeEditor = tinymce.activeEditor;
            if (activeEditor) {
                activeEditor.insertContent(`[${placeholder}]`);
            }
        });
    });

    // Quick action buttons
    document.getElementById('insertHeaderBtn').addEventListener('click', function() {
        const headerContent = `
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">REPUBLIC OF THE PHILIPPINES</h1>
                <h2 style="font-size: 16px; margin-bottom: 5px;">Province of [province_name]</h2>
                <h2 style="font-size: 16px; margin-bottom: 5px;">Municipality of [municipality_name]</h2>
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">BARANGAY [barangay_name]</h1>
            </div>
        `;
        tinymce.get('header_content').setContent(headerContent);
    });

    document.getElementById('insertSignatureBtn').addEventListener('click', function() {
        const signatureContent = `
            <div style="margin-top: 50px; text-align: right;">
                <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; margin-bottom: 10px;"></div>
                <p style="font-weight: bold; margin-bottom: 5px;">[official_name]</p>
                <p style="font-size: 14px; color: #666;">[official_position]</p>
            </div>
        `;
        tinymce.get('footer_content').setContent(signatureContent);
    });

    document.getElementById('insertDateBtn').addEventListener('click', function() {
        const dateContent = `
            <p>Issued this [current_date] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
        `;
        const activeEditor = tinymce.activeEditor;
        if (activeEditor) {
            activeEditor.insertContent(dateContent);
        }
    });

    // Update summary and preview
    function updateSummary() {
        const documentType = document.getElementById('document_type').value;
        const customType = document.getElementById('custom_document_type').value;
        const category = document.getElementById('template_category').value;
        const description = document.getElementById('description').value;

        document.getElementById('summary_document_type').textContent = documentType === 'Other' ? customType : documentType;
        document.getElementById('summary_category').textContent = category.charAt(0).toUpperCase() + category.slice(1);
        document.getElementById('summary_description').textContent = description || 'No description provided';
    }

    function updatePreview() {
        const headerContent = tinymce.get('header_content').getContent();
        const bodyContent = tinymce.get('body_content').getContent();
        const footerContent = tinymce.get('footer_content').getContent();

        // Sample data for preview
        const sampleData = {
            'resident_name': 'Juan Dela Cruz',
            'resident_address': '123 Sample Street, Barangay Sample',
            'civil_status': 'Married',
            'purpose': 'employment purposes',
            'document_purpose': 'employment purposes',
            'barangay_name': 'Sample Barangay',
            'municipality_name': 'Sample Municipality',
            'province_name': 'Sample Province',
            'official_name': 'Hon. Sample Official',
            'official_position': 'Barangay Captain',
            'current_date': 'January 15, 2024'
        };

        // Replace placeholders with sample data
        let previewContent = headerContent + bodyContent + footerContent;
        Object.entries(sampleData).forEach(([key, value]) => {
            previewContent = previewContent.replace(new RegExp(`\\[${key}\\]`, 'g'), value);
        });

        document.getElementById('finalPreview').innerHTML = previewContent;
    }

    // Form submission handling
    document.getElementById('templateWizardForm').addEventListener('submit', function(e) {
        // Set the final document type value
        const documentType = document.getElementById('document_type').value;
        if (documentType === 'Other') {
            const customType = document.getElementById('custom_document_type').value;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'document_type';
            hiddenInput.value = customType;
            this.appendChild(hiddenInput);
        }
    });
});
</script>
@endpush 