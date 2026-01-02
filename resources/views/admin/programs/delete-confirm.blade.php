@extends('admin.main.layout')

@section('title', 'Delete Program')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">Delete Program</h1>
                    <p class="text-gray-600">Are you sure you want to delete this program?</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h2 class="font-semibold text-gray-900 mb-2">{{ $program->name }}</h2>
                <p class="text-sm text-gray-600 mb-2">
                    <span class="font-medium">Type:</span> {{ ucfirst($program->type) }}
                </p>
                @if($program->description)
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Description:</span> {{ Str::limit($program->description, 100) }}
                    </p>
                @endif
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. The program will be permanently deleted from the system.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.programs.manage.destroy', $program->id) }}" class="flex justify-end gap-4">
                @csrf
                @method('DELETE')
                
                <a href="{{ route('admin.programs.manage.index') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete Program
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

