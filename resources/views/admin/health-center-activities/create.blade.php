@extends('admin.main.layout')

@section('title', 'Create Health Center Activity')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Skeleton Component -->
    <div id="hcaCreateSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'health-activity'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="hcaCreateContent" style="display: none;">
        <!-- Header -->
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Create Health Center Activity</h1>
                <p class="text-gray-600">Add a new planned or completed health activity</p>
            </div>
        </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form action="{{ route('admin.health-center-activities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Activity Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-notes-medical text-blue-600 mr-2"></i>
                    Activity Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="activity_name" class="block text-sm font-medium text-gray-700 mb-2">Activity Name <span class="text-red-500">*</span></label>
                        <input type="text" name="activity_name" id="activity_name" value="{{ old('activity_name') }}" placeholder="Example: Vaccination Drive, Health Check-up" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-2">Activity Type <span class="text-red-500">*</span></label>
                        <select name="activity_type" id="activity_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select activity type...</option>
                            <option value="Vaccination" {{ old('activity_type') == 'Vaccination' ? 'selected' : '' }}>Vaccination</option>
                            <option value="Health Check-up" {{ old('activity_type') == 'Health Check-up' ? 'selected' : '' }}>Health Check-up</option>
                            <option value="Health Education" {{ old('activity_type') == 'Health Education' ? 'selected' : '' }}>Health Education</option>
                            <option value="Medical Consultation" {{ old('activity_type') == 'Medical Consultation' ? 'selected' : '' }}>Medical Consultation</option>
                            <option value="Emergency Response" {{ old('activity_type') == 'Emergency Response' ? 'selected' : '' }}>Emergency Response</option>
                            <option value="Preventive Care" {{ old('activity_type') == 'Preventive Care' ? 'selected' : '' }}>Preventive Care</option>
                            <option value="Maternal Care" {{ old('activity_type') == 'Maternal Care' ? 'selected' : '' }}>Maternal Care</option>
                            <option value="Child Care" {{ old('activity_type') == 'Child Care' ? 'selected' : '' }}>Child Care</option>
                            <option value="Other" {{ old('activity_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date and Time -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-calendar text-green-600 mr-2"></i>
                    Date & Time
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="activity_date" class="block text-sm font-medium text-gray-700 mb-2">Activity Date <span class="text-red-500">*</span></label>
                        <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Example: Health Center, Barangay Hall" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    </div>
                </div>
            </div>

            <!-- Description & Objectives -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-align-left text-purple-600 mr-2"></i>
                    Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter activity description" required>{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="objectives" class="block text-sm font-medium text-gray-700 mb-2">Objectives</label>
                        <textarea name="objectives" id="objectives" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter objectives">{{ old('objectives') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-image text-pink-600 mr-2"></i>
                    Activity Image
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                        <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        <p class="mt-1 text-sm text-gray-500">Upload an image for the activity (JPG, PNG, GIF). Max size: 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Target Audience & Expected Participants -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-users text-yellow-600 mr-2"></i>
                    Audience
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="all"
                                       {{ old('audience_scope', 'all') === 'all' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500" required>
                                <span class="ml-2 text-sm text-gray-700">All Residents</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="purok"
                                       {{ old('audience_scope') === 'purok' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500" required>
                                <span class="ml-2 text-sm text-gray-700">Specific Purok</span>
                            </label>
                            <div id="audiencePurokWrapper" class="mt-2 {{ old('audience_scope', 'all') === 'purok' ? '' : 'hidden' }}">
                                <label for="audience_purok" class="block text-sm font-medium text-gray-700 mb-1">Select Purok</label>
                                <select name="audience_purok" id="audience_purok"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select Purok...</option>
                                    @for($i = 1; $i <= 7; $i++)
                                        <option value="{{ $i }}" {{ old('audience_purok') == $i ? 'selected' : '' }}>Purok {{ $i }}</option>
                                    @endfor
                                </select>
                                <p class="mt-1 text-xs text-gray-500">This activity will primarily target residents from the selected Purok.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="target_participants" class="block text-sm font-medium text-gray-700 mb-2">Target Participants</label>
                        <input type="number" name="target_participants" id="target_participants"
                               value="{{ old('target_participants') }}" min="1"
                               placeholder="e.g. 50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                </div>
            </div>

            <!-- Resources & Team -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-toolbox text-teal-600 mr-2"></i>
                    Resources & Team
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="required_resources" class="block text-sm font-medium text-gray-700 mb-2">Required Resources</label>
                        <textarea name="required_resources" id="required_resources" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Enter required resources">{{ old('required_resources') }}</textarea>
                    </div>
                    <div>
                        <label for="staff_involved" class="block text-sm font-medium text-gray-700 mb-2">Staff Involved</label>
                        <textarea name="staff_involved" id="staff_involved" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Enter staff involved">{{ old('staff_involved') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Organizer & Status -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>
                    Organizer & Status
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-700">
                            Automatically set based on activity date and time (Planned → Ongoing → Completed). Use the cancel action separately if needed.
                        </div>
                    </div>
                    <div>
                        <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Organizer</label>
                        <input type="text" name="organizer" id="organizer" value="{{ old('organizer') }}" placeholder="Enter organizer name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Mark as featured activity</span>
                    </label>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500" placeholder="Enter notes">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between mt-8">
                <a href="{{ route('admin.health-center-activities.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Save Activity
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeInputs = document.querySelectorAll('input[name="audience_scope"]');
    const purokWrapper = document.getElementById('audiencePurokWrapper');

    function updateAudienceVisibility() {
        const selected = document.querySelector('input[name="audience_scope"]:checked');
        if (!selected || !purokWrapper) return;
        if (selected.value === 'purok') {
            purokWrapper.classList.remove('hidden');
        } else {
            purokWrapper.classList.add('hidden');
        }
    }

    scopeInputs.forEach(input => {
        input.addEventListener('change', updateAudienceVisibility);
    });

    updateAudienceVisibility();

    setTimeout(() => {
        const skeleton = document.getElementById('hcaCreateSkeleton');
        const content = document.getElementById('hcaCreateContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 