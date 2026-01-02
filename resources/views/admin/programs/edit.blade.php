@extends('admin.main.layout')

@section('title', 'Edit Program')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-edit mr-2"></i>
            Edit Program
        </h1>
        <p class="text-gray-600">
            Update program information and eligibility criteria
        </p>
    </div>

    <form method="POST" action="{{ route('admin.programs.manage.update', $program->id) }}" id="programForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Program Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $program->name) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Program Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="employment" {{ old('type', $program->type) === 'employment' ? 'selected' : '' }}>Employment</option>
                        <option value="health" {{ old('type', $program->type) === 'health' ? 'selected' : '' }}>Health</option>
                        <option value="education" {{ old('type', $program->type) === 'education' ? 'selected' : '' }}>Education</option>
                        <option value="social" {{ old('type', $program->type) === 'social' ? 'selected' : '' }}>Social</option>
                        <option value="safety" {{ old('type', $program->type) === 'safety' ? 'selected' : '' }}>Safety</option>
                        <option value="custom" {{ old('type', $program->type) === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $program->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority (0-10)
                    </label>
                    <input type="number" id="priority" name="priority" value="{{ old('priority', $program->priority) }}" min="0" max="10"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('priority') border-red-500 @enderror">
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center mt-6">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active Program
                    </label>
                </div>
            </div>
        </div>

        <!-- Criteria Builder -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Eligibility Criteria</h2>
            <p class="text-sm text-gray-600 mb-4">
                Define the conditions that determine resident eligibility for this program. You can combine conditions using AND/OR logic.
            </p>
            
            <div id="criteria-builder">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Root Operator
                    </label>
                    <select id="root-operator" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="AND" {{ (old('criteria', $program->criteria)['operator'] ?? 'AND') === 'AND' ? 'selected' : '' }}>AND (all conditions must be true)</option>
                        <option value="OR" {{ (old('criteria', $program->criteria)['operator'] ?? 'AND') === 'OR' ? 'selected' : '' }}>OR (at least one condition must be true)</option>
                    </select>
                </div>
                
                <div id="conditions-container" class="space-y-4">
                    <!-- Conditions will be loaded here dynamically -->
                </div>
                
                <div class="mt-4 flex gap-2">
                    <button type="button" onclick="addCondition()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Condition
                    </button>
                    <button type="button" onclick="addGroup()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-layer-group mr-2"></i>Add Group
                    </button>
                </div>
            </div>
            
            <!-- Hidden input to store criteria JSON -->
            <input type="hidden" name="criteria" id="criteria-json" value="{{ old('criteria', json_encode($program->criteria)) }}">
            
            @error('criteria')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Target Puroks -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Target Puroks (Optional)</h2>
            <p class="text-sm text-gray-600 mb-4">
                Select specific puroks to target. Leave empty to target all puroks.
            </p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($puroks as $purok)
                    <label class="flex items-center">
                        <input type="checkbox" name="target_puroks[]" value="{{ $purok['token'] }}"
                               {{ in_array($purok['token'], old('target_puroks', $program->target_puroks ?? [])) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $purok['name'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.programs.manage.index') }}" 
               class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Update Program
            </button>
        </div>
    </form>
</div>

<script src="{{ asset('js/program-criteria-builder.js') }}"></script>
<script>
    // Initialize criteria builder with existing criteria
    document.addEventListener('DOMContentLoaded', function() {
        const existingCriteria = @json(old('criteria', $program->criteria));
        initializeCriteriaBuilder(existingCriteria);
    });
</script>
@endsection

