@extends('admin.main.layout')

@section('title', 'Template Builder - ' . $template->template_type)

@section('content')
<div class="max-w-6xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Template Builder</h1>
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

    <!-- Info Message -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>
            Use the simple editor view for easier editing. This builder view provides a visual interface for template customization.
        </p>
    </div>

    <!-- Redirect to Edit -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <i class="fas fa-code text-gray-400 text-5xl mb-4"></i>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Template Builder</h2>
        <p class="text-gray-600 mb-6">Use the edit page for template customization.</p>
        <a href="{{ route('admin.blotter-templates.edit', $template) }}" 
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fas fa-edit mr-2"></i>
            Go to Edit Page
        </a>
    </div>
</div>
@endsection

