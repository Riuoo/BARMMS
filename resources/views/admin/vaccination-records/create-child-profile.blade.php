@extends('admin.main.layout')

@section('title', 'Create Child Profile')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <!-- Form Skeleton -->
    <div id="createChildProfileFormSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'showButton' => false])
        @include('components.loading.create-form-skeleton', ['type' => 'child-profile'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="createChildProfileContent" style="display: none;">
    <h1 class="text-3xl font-bold mb-8">Create Child Profile</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.vaccination-records.store-child-profile') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div class="mb-2 p-3 rounded border border-red-300 bg-red-50 text-red-700">
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
                                            <input type="number" name="contact_number" value="{{ old('contact_number') }}" class="w-full border rounded px-3 py-2" placeholder="Example: 9191234567" min="0" pattern="[0-9]*" inputmode="numeric">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Purok *</label>
                    <input type="text" name="purok" value="{{ old('purok') }}" class="w-full border rounded px-3 py-2" required>
                    @error('purok')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-between mt-8">
                <a href="{{ route('admin.vaccination-records.child-profiles') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
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
        const formSkeleton = document.getElementById('createChildProfileFormSkeleton');
        const content = document.getElementById('createChildProfileContent');
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection
