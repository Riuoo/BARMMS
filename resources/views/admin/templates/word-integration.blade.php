@extends('admin.main.layout')

@section('title', 'Word Document Integration - ' . $template->document_type)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Word Document Integration</h1>
                <p class="text-gray-600">{{ $template->document_type }} - Edit with Microsoft Word</p>
            </div>
            <div class="mt-4 sm:mt-0 space-x-2">
                <a href="{{ route('admin.templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Templates
                </a>
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Word Integration Options -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Option 1: Download & Upload -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-download text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Download & Upload</h3>
                        <p class="text-sm text-gray-600">Edit in Microsoft Word</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">How it works:</h4>
                        <ol class="text-sm text-blue-800 space-y-1">
                            <li>1. Download the HTML document template</li>
                            <li>2. Open in Microsoft Word (File → Open)</li>
                            <li>3. Edit the document as needed</li>
                            <li>4. Save as HTML or plain text</li>
                            <li>5. Upload the edited document</li>
                        </ol>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('admin.templates.download-word', $template) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Download HTML Template
                        </a>

                        <form action="{{ route('admin.templates.upload-word', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label for="word_file" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Edited Document
                                </label>
                                <input type="file" id="word_file" name="word_file" accept=".html,.htm,.txt,.docx" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Supported formats: .html, .htm, .txt, .docx</p>
                            </div>
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                <i class="fas fa-upload mr-2"></i>
                                Upload & Update Template
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Option 2: Online Editor -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Online Editor</h3>
                        <p class="text-sm text-gray-600">Edit in browser</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">Features:</h4>
                        <ul class="text-sm text-green-800 space-y-1">
                            <li>• Word-like interface in browser</li>
                            <li>• Real-time preview</li>
                            <li>• Placeholder insertion tools</li>
                            <li>• No software installation needed</li>
                        </ul>
                    </div>

                    <a href="{{ route('admin.templates.edit', $template) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Open Online Editor
                    </a>
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
                        @foreach($template->getAvailablePlaceholders() as $key => $description)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <div>
                                    <code class="text-sm font-mono text-blue-600">[{{ $key }}]</code>
                                    <p class="text-xs text-gray-600">{{ $description }}</p>
                                </div>
                                <button onclick="copyPlaceholder('{{ $key }}')" 
                                        class="text-xs text-gray-500 hover:text-blue-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Template Preview</h4>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="text-sm text-gray-600 mb-2">Last updated: {{ $template->updated_at->format('M d, Y H:i') }}</div>
                        <button type="button" 
                                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 js-preview-template"
                                data-template-id="{{ $template->id }}">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Template
                        </button>
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
                    <li>• Open HTML file in Word: File → Open → Select HTML file</li>
                    <li>• Edit normally - placeholders will be preserved</li>
                    <li>• Save as HTML: File → Save As → Web Page (.html)</li>
                    <li>• Or save as plain text if you prefer</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Template Preview</h3>
            <button id="closePreview" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="previewContent" class="border border-gray-200 rounded-lg p-6 bg-white" style="min-height: 500px;">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyPlaceholder(placeholder) {
    navigator.clipboard.writeText(`[${placeholder}]`).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('text-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-600');
        }, 1000);
    });
}

function previewTemplate(templateId) {
    document.getElementById('previewContent').innerHTML = '<div class="flex items-center justify-center h-64"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    document.getElementById('previewModal').classList.remove('hidden');

    fetch(`/admin/templates/${templateId}/preview`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('previewContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('previewContent').innerHTML = '<div class="text-center text-red-600">Error loading preview</div>';
        });
}

// Delegated click for preview button
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.js-preview-template');
    if (!btn) return;
    const id = btn.getAttribute('data-template-id');
    if (id) {
        previewTemplate(id);
    }
});

document.getElementById('closePreview').addEventListener('click', function() {
    document.getElementById('previewModal').classList.add('hidden');
});

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
