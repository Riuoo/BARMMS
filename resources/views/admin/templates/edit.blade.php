@extends('admin.modals.layout')

@section('title', 'Edit Template - ' . $template->document_type)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Template</h1>
                <p class="text-gray-600">{{ $template->document_type }}</p>
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

    <!-- Template Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Template Information</h2>
                
                <!-- Template Status -->
                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Database Template</h3>
                            <p class="text-sm text-blue-700 mt-1">
                                This template uses the database content for document generation. All templates are managed through the database system.
                            </p>
                        </div>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4">
                    This template is managed through the database system. The content is automatically used when generating documents for this document type.
                </p>
            </div>

            <!-- Available Placeholders -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Placeholders</h3>
                <p class="text-gray-600 mb-4">
                    These placeholders are automatically replaced with actual data when generating documents.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                    @foreach($template->getAvailablePlaceholders() as $key => $description)
                        <div class="flex items-start space-x-2">
                            <code class="px-2 py-1 bg-white border border-gray-200 rounded text-sm font-mono select-all">[{{ $key }}]</code>
                            <span class="text-sm text-gray-600">{{ $description }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reset Button -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <form action="{{ route('admin.templates.reset', $template) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to reset this template to default? All customizations will be lost.')"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-200">
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Default Template
                    </button>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Template System Information</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">How Templates Work</h4>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Templates are stored in the database</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Placeholders are automatically replaced with real data</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Documents are generated using the database template content</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> All templates are managed through the admin interface</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Template Features</h4>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        <li><i class="fas fa-info-circle text-blue-400 mr-2"></i> Automatic placeholder replacement</li>
                        <li><i class="fas fa-info-circle text-blue-400 mr-2"></i> Consistent formatting across all documents</li>
                        <li><i class="fas fa-info-circle text-blue-400 mr-2"></i> Easy template management</li>
                        <li><i class="fas fa-info-circle text-blue-400 mr-2"></i> Reliable document generation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 