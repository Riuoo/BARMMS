@extends('admin.main.layout')

@section('title', 'Document Templates')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Templates</h1>
                <p class="text-gray-600">Manage and customize document templates</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Template
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

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $template->document_type }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Template Info -->
                        <div>
                            <p class="text-sm text-gray-500">Last updated: {{ $template->updated_at->format('M d, Y H:i A') }}</p>
                            <p class="text-sm text-gray-500">Placeholders: {{ count($template->placeholders ?? []) }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.templates.edit', $template) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>

                            <form action="{{ route('admin.templates.reset', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to reset this template to default?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-200">
                                    <i class="fas fa-undo mr-1"></i>
                                    Reset
                                </button>
                            </form>

                            <form action="{{ route('admin.templates.toggle-status', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                                    <i class="fas {{ $template->is_active ? 'fa-toggle-on text-green-600' : 'fa-toggle-off text-gray-400' }} mr-1"></i>
                                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No templates found</h3>
                <p class="text-gray-500">Get started by creating your first template.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Template
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection 