@extends('admin.main.layout')

@section('title', 'Create Health Center Activity')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header -->
    <div class="mb-3 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Create Health Center Activity</h1>
            <p class="text-gray-600">Add a new planned or completed health activity</p>
        </div>
        <a href="{{ route('admin.health-center-activities.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
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
        <form action="{{ route('admin.health-center-activities.store') }}" method="POST" class="space-y-6">
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
                        <input type="text" name="activity_name" id="activity_name" value="{{ old('activity_name') }}" placeholder="e.g., Vaccination Drive, Health Check-up" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-2">Activity Type <span class="text-red-500">*</span></label>
                        <select name="activity_type" id="activity_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="e.g., Health Center, Barangay Hall" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                        <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Detailed description of the activity...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="objectives" class="block text-sm font-medium text-gray-700 mb-2">Objectives</label>
                        <textarea name="objectives" id="objectives" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Goals and objectives of the activity...">{{ old('objectives') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Target & Expected Participants -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-users text-yellow-600 mr-2"></i>
                    Audience
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="target_population" class="block text-sm font-medium text-gray-700 mb-2">Target Population</label>
                        <select name="target_population" id="target_population" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Select target population...</option>
                            <option value="All Residents" {{ old('target_population') == 'All Residents' ? 'selected' : '' }}>All Residents</option>
                            <option value="Children (0-12 years)" {{ old('target_population') == 'Children (0-12 years)' ? 'selected' : '' }}>Children (0-12 years)</option>
                            <option value="Adolescents (13-19 years)" {{ old('target_population') == 'Adolescents (13-19 years)' ? 'selected' : '' }}>Adolescents (13-19 years)</option>
                            <option value="Adults (20-59 years)" {{ old('target_population') == 'Adults (20-59 years)' ? 'selected' : '' }}>Adults (20-59 years)</option>
                            <option value="Seniors (60+ years)" {{ old('target_population') == 'Seniors (60+ years)' ? 'selected' : '' }}>Seniors (60+ years)</option>
                            <option value="Pregnant Women" {{ old('target_population') == 'Pregnant Women' ? 'selected' : '' }}>Pregnant Women</option>
                            <option value="Infants" {{ old('target_population') == 'Infants' ? 'selected' : '' }}>Infants</option>
                            <option value="Other" {{ old('target_population') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="expected_participants" class="block text-sm font-medium text-gray-700 mb-2">Expected Participants</label>
                        <input type="number" name="expected_participants" id="expected_participants" value="{{ old('expected_participants') }}" min="1" placeholder="Estimated number of participants" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
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
                        <textarea name="required_resources" id="required_resources" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Equipment, supplies, materials needed...">{{ old('required_resources') }}</textarea>
                    </div>
                    <div>
                        <label for="staff_involved" class="block text-sm font-medium text-gray-700 mb-2">Staff Involved</label>
                        <textarea name="staff_involved" id="staff_involved" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Healthcare staff, volunteers, etc...">{{ old('staff_involved') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Status & Organizer -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>
                    Status & Organizer
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select status...</option>
                            <option value="Planned" {{ old('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
                            <option value="Ongoing" {{ old('status') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Organizer</label>
                        <input type="text" name="organizer" id="organizer" value="{{ old('organizer') }}" placeholder="Name of organizer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500" placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
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
@endsection 