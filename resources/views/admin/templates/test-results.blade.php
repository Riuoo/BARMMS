@extends('admin.main.layout')

@section('title', 'Test Template - ' . $template->document_type)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Template Test Results</h1>
                <p class="text-gray-600">{{ $template->document_type }} - Testing with multiple scenarios</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.templates.edit', $template) }}" 
                   class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    <div class="space-y-6">
        @foreach($results as $scenario => $result)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 capitalize">{{ $scenario }} Scenario</h2>
                        @if($result['valid'])
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Valid
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Issues Found
                            </span>
                        @endif
                    </div>

                    @if(!empty($result['unreplaced']))
                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Unreplaced Placeholders:
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($result['unreplaced'] as $placeholder)
                                    <code class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">[{{ $placeholder }}]</code>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Preview -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-700">Preview</h3>
                        </div>
                        <div class="p-6 bg-white" style="min-height: 400px;">
                            <iframe 
                                srcdoc="{{ htmlspecialchars($result['html'], ENT_QUOTES, 'UTF-8') }}"
                                class="w-full border-0"
                                style="min-height: 400px;"
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="mt-8 bg-blue-50 rounded-lg border border-blue-200 p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>
            Test Summary
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4">
                <div class="text-sm text-gray-500">Total Scenarios</div>
                <div class="text-2xl font-bold text-gray-900">{{ count($results) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4">
                <div class="text-sm text-gray-500">Valid Scenarios</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ collect($results)->where('valid', true)->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg p-4">
                <div class="text-sm text-gray-500">Issues Found</div>
                <div class="text-2xl font-bold text-red-600">
                    {{ collect($results)->where('valid', false)->count() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

