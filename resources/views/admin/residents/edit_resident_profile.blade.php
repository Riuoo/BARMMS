@extends('admin.modals.layout')

@section('title', 'Edit Resident Profile')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Resident Profile</h1>

        <form action="{{ route('admin.residents.update', $resident->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-medium mb-1">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $resident->name) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $resident->email) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="role" class="block font-medium mb-1">Role</label>
                <input type="text" id="role" name="role" value="{{ old('role', $resident->role) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block font-medium mb-1">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address', $resident->address) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Demographic Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Demographic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="age" class="block font-medium mb-1">Age</label>
                        <input type="number" id="age" name="age" value="{{ old('age', $resident->age) }}" min="1" max="120" class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label for="family_size" class="block font-medium mb-1">Family Size</label>
                        <input type="number" id="family_size" name="family_size" value="{{ old('family_size', $resident->family_size) }}" min="1" max="20" class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label for="education_level" class="block font-medium mb-1">Education Level</label>
                        <select id="education_level" name="education_level" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Education Level</option>
                            <option value="No Education" {{ old('education_level', $resident->education_level) == 'No Education' ? 'selected' : '' }}>No Education</option>
                            <option value="Elementary" {{ old('education_level', $resident->education_level) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ old('education_level', $resident->education_level) == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Vocational" {{ old('education_level', $resident->education_level) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="College" {{ old('education_level', $resident->education_level) == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ old('education_level', $resident->education_level) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="income_level" class="block font-medium mb-1">Income Level</label>
                        <select id="income_level" name="income_level" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Income Level</option>
                            <option value="Low" {{ old('income_level', $resident->income_level) == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Lower Middle" {{ old('income_level', $resident->income_level) == 'Lower Middle' ? 'selected' : '' }}>Lower Middle</option>
                            <option value="Middle" {{ old('income_level', $resident->income_level) == 'Middle' ? 'selected' : '' }}>Middle</option>
                            <option value="Upper Middle" {{ old('income_level', $resident->income_level) == 'Upper Middle' ? 'selected' : '' }}>Upper Middle</option>
                            <option value="High" {{ old('income_level', $resident->income_level) == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="employment_status" class="block font-medium mb-1">Employment Status</label>
                        <select id="employment_status" name="employment_status" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Employment Status</option>
                            <option value="Unemployed" {{ old('employment_status', $resident->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Part-time" {{ old('employment_status', $resident->employment_status) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Self-employed" {{ old('employment_status', $resident->employment_status) == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Full-time" {{ old('employment_status', $resident->employment_status) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="health_status" class="block font-medium mb-1">Health Status</label>
                        <select id="health_status" name="health_status" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Health Status</option>
                            <option value="Critical" {{ old('health_status', $resident->health_status) == 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="Poor" {{ old('health_status', $resident->health_status) == 'Poor' ? 'selected' : '' }}>Poor</option>
                            <option value="Fair" {{ old('health_status', $resident->health_status) == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Good" {{ old('health_status', $resident->health_status) == 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Excellent" {{ old('health_status', $resident->health_status) == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="block font-medium mb-1">Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block font-medium mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.residents') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
            </div>
        </form>
    </div>
@endsection 