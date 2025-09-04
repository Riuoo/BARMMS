@extends('admin.main.layout')

@section('title', 'Create Child Profile')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <!-- Header Skeleton -->
    <div id="createChildProfileHeaderSkeleton" class="animate-pulse mb-6">
        <div class="h-8 w-64 bg-gray-200 rounded"></div>
    </div>

    <!-- Form Skeleton -->
    <div id="createChildProfileFormSkeleton" class="animate-pulse bg-white rounded-lg shadow p-6">
        <!-- Error Messages Skeleton (if any) -->
        <div class="mb-4 p-3 rounded border border-gray-200 bg-gray-50">
            <div class="h-4 w-48 bg-gray-200 rounded mb-2"></div>
            <div class="space-y-1">
                <div class="h-3 w-64 bg-gray-100 rounded"></div>
                <div class="h-3 w-56 bg-gray-100 rounded"></div>
            </div>
        </div>
        
        <!-- Form Fields Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- First Name -->
            <div>
                <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Last Name -->
            <div>
                <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Birth Date -->
            <div>
                <div class="h-4 w-20 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Gender -->
            <div>
                <div class="h-4 w-16 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Mother's Name -->
            <div>
                <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Contact Number -->
            <div>
                <div class="h-4 w-28 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
            
            <!-- Purok -->
            <div>
                <div class="h-4 w-16 bg-gray-200 rounded mb-2"></div>
                <div class="h-10 w-full bg-gray-200 rounded"></div>
            </div>
        </div>
        
        <!-- Form Actions Skeleton -->
        <div class="mt-6 flex justify-end space-x-3">
            <div class="h-10 w-20 bg-gray-200 rounded"></div>
            <div class="h-10 w-32 bg-gray-200 rounded"></div>
        </div>
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createChildProfileContent" style="display: none;">
    <h1 class="text-3xl font-bold mb-6">Create Child Profile</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.vaccination-records.store-child-profile') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-700">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('first_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('last_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Birth Date *</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border rounded px-3 py-2" required>
                    @error('birth_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Gender *</label>
                    <select name="gender" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select...</option>
                        <option value="Male" {{ old('gender')==='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender')==='Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Mother's Name *</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('mother_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Contact Number</label>
                                            <input type="number" name="contact_number" value="{{ old('contact_number') }}" class="w-full border rounded px-3 py-2" placeholder="e.g., 9191234567" min="0" pattern="[0-9]*" inputmode="numeric">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Purok *</label>
                    <input type="text" name="purok" value="{{ old('purok') }}" class="w-full border rounded px-3 py-2" required>
                    @error('purok')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.vaccination-records.child-profiles') }}" 
                   class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create Profile
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('createChildProfileHeaderSkeleton');
        const formSkeleton = document.getElementById('createChildProfileFormSkeleton');
        const content = document.getElementById('createChildProfileContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection
