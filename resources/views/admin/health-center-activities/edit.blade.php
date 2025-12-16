@extends('admin.main.layout')

@section('title', 'Edit Health Center Activity')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Skeleton Component -->
        <div id="hcaEditSkeleton">
            @include('components.loading.edit-form-skeleton', ['type' => 'header', 'showButton' => false])
            @include('components.loading.edit-form-skeleton', ['type' => 'health-activity'])
        </div>

        <!-- Real Content (hidden initially) -->
        <div id="hcaEditContent" style="display: none;">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Edit Health Center Activity</h1>
            </div>

        <div class="bg-white rounded-lg shadow p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.health-center-activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Activity Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="activity_name" class="block text-sm font-medium text-gray-700 mb-2">Activity Name <span class="text-red-500">*</span></label>
                        <input type="text" name="activity_name" id="activity_name" 
                               value="{{ old('activity_name', $activity->activity_name) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: Vaccination Drive, Health Check-up" required>
                    </div>

                    <div>
                        <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-2">Activity Type <span class="text-red-500">*</span></label>
                        <select name="activity_type" id="activity_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select activity type...</option>
                            <option value="Vaccination" {{ old('activity_type', $activity->activity_type) == 'Vaccination' ? 'selected' : '' }}>Vaccination</option>
                            <option value="Health Check-up" {{ old('activity_type', $activity->activity_type) == 'Health Check-up' ? 'selected' : '' }}>Health Check-up</option>
                            <option value="Health Education" {{ old('activity_type', $activity->activity_type) == 'Health Education' ? 'selected' : '' }}>Health Education</option>
                            <option value="Medical Consultation" {{ old('activity_type', $activity->activity_type) == 'Medical Consultation' ? 'selected' : '' }}>Medical Consultation</option>
                            <option value="Emergency Response" {{ old('activity_type', $activity->activity_type) == 'Emergency Response' ? 'selected' : '' }}>Emergency Response</option>
                            <option value="Preventive Care" {{ old('activity_type', $activity->activity_type) == 'Preventive Care' ? 'selected' : '' }}>Preventive Care</option>
                            <option value="Maternal Care" {{ old('activity_type', $activity->activity_type) == 'Maternal Care' ? 'selected' : '' }}>Maternal Care</option>
                            <option value="Child Care" {{ old('activity_type', $activity->activity_type) == 'Child Care' ? 'selected' : '' }}>Child Care</option>
                            <option value="Other" {{ old('activity_type', $activity->activity_type) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="activity_date" class="block text-sm font-medium text-gray-700 mb-2">Activity Date <span class="text-red-500">*</span></label>
                        <input type="date" name="activity_date" id="activity_date" 
                               value="{{ old('activity_date', $activity->activity_date->format('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" 
                               value="{{ old('start_time', $activity->start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $activity->start_time)->format('H:i') : '') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" 
                               value="{{ old('end_time', $activity->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $activity->end_time)->format('H:i') : '') }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" 
                               value="{{ old('location', $activity->location) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2" 
                               placeholder="Example: Health Center, Barangay Hall" required>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2" 
                              placeholder="Enter activity description" required>{{ old('description', $activity->description) }}</textarea>
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Activity Image</label>
                    @if($activity->image)
                        <div class="mb-3">
                            <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                            <img src="{{ $activity->image_url }}" alt="Current activity image" class="w-32 h-24 object-cover rounded border">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-gray-100 cursor-not-allowed" disabled>
                    <p class="mt-1 text-sm text-gray-500">Image uploads are locked for this form.</p>
                </div>

                <!-- Objectives -->
                <div class="mb-6">
                    <label for="objectives" class="block text-sm font-medium text-gray-700 mb-2">Objectives</label>
                    <textarea name="objectives" id="objectives" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                              placeholder="Enter objectives" readonly>{{ old('objectives', $activity->objectives) }}</textarea>
                </div>

                <!-- Target Audience & Target Participants -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience *</label>
                        @php
                            $currentAudienceScope = old('audience_scope', $activity->audience_scope ?? 'all');
                        @endphp
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="all"
                                       {{ $currentAudienceScope === 'all' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                <span class="ml-2 text-sm text-gray-700">All Residents</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="audience_scope" value="purok"
                                       {{ $currentAudienceScope === 'purok' ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                <span class="ml-2 text-sm text-gray-700">Specific Purok</span>
                            </label>
                            <div id="audiencePurokWrapperEdit" class="mt-2 {{ $currentAudienceScope === 'purok' ? '' : 'hidden' }}">
                                <label for="audience_purok" class="block text-sm font-medium text-gray-700 mb-1">Select Purok</label>
                                @php
                                    $currentAudiencePurok = old('audience_purok', $activity->audience_purok);
                                @endphp
                                <select name="audience_purok" id="audience_purok"
                                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select Purok...</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ (string)$currentAudiencePurok === (string)$i ? 'selected' : '' }}>Purok {{ $i }}</option>
                                    @endfor
                                </select>
                                <p class="mt-1 text-xs text-gray-500">This activity will primarily target residents from the selected Purok.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="target_participants" class="block text-sm font-medium text-gray-700 mb-2">Target Participants</label>
                        <input type="number" name="target_participants" id="target_participants" 
                               value="{{ old('target_participants', $activity->target_participants) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                               placeholder="e.g. 50" min="1" readonly>
                    </div>
                </div>

                <!-- Resources and Staff -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="required_resources" class="block text-sm font-medium text-gray-700 mb-2">Required Resources</label>
                    <textarea name="required_resources" id="required_resources" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                              placeholder="Enter required resources">{{ old('required_resources', $activity->required_resources) }}</textarea>
                    </div>

                    <div>
                        <label for="staff_involved" class="block text-sm font-medium text-gray-700 mb-2">Staff Involved</label>
                        <textarea name="staff_involved" id="staff_involved" rows="3" 
                                  class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                                  placeholder="Enter staff involved" readonly>{{ old('staff_involved', $activity->staff_involved) }}</textarea>
                    </div>
                </div>

                <!-- Organizer & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center gap-2">
                            @php
                                $statusStyles = [
                                    'Planned' => 'bg-blue-100 text-blue-800',
                                    'Ongoing' => 'bg-yellow-100 text-yellow-800',
                                    'Completed' => 'bg-green-100 text-green-800',
                                    'Cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusClass = $statusStyles[$activity->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                                {{ $activity->status }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Status updates automatically based on the activity date and time.</p>
                    </div>
                    <div>
                        <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Organizer</label>
                        <input type="text" name="organizer" id="organizer" 
                               value="{{ old('organizer', $activity->organizer) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                               placeholder="Enter organizer name" readonly>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $activity->is_featured) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Mark as featured activity</span>
                    </label>
                </div>

                <!-- Additional Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
                              placeholder="Enter notes" readonly>{{ old('notes', $activity->notes) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between mt-8">
                    <a href="{{ route('admin.health-center-activities.show', $activity->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Health Center Activity
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeInputs = document.querySelectorAll('input[name="audience_scope"]');
    const purokWrapper = document.getElementById('audiencePurokWrapperEdit');

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
        const skeleton = document.getElementById('hcaEditSkeleton');
        const content = document.getElementById('hcaEditContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection 