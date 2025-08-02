@extends('resident.layout')

@section('title', 'Report Health Concerns')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Report Health Concerns</h1>
                <p class="text-gray-600">Report health-related concerns to barangay health officials for proper assistance</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
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

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Health Report Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-heartbeat text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900">Health Report Form</h2>
                        <p class="text-sm text-gray-500">Provide detailed information about your health concern</p>
                    </div>
                </div>

                <form action="{{ route('resident.health-status') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Concern Type -->
                    <div>
                        <label for="concern_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-2 text-gray-400"></i>
                            Type of Concern <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                                id="concern_type" 
                                name="concern_type" 
                                required>
                            <option value="">Select a concern type</option>
                            <option value="Fever" {{ old('concern_type') == 'Fever' ? 'selected' : '' }}>Fever</option>
                            <option value="Cough/Cold" {{ old('concern_type') == 'Cough/Cold' ? 'selected' : '' }}>Cough/Cold</option>
                            <option value="Difficulty Breathing" {{ old('concern_type') == 'Difficulty Breathing' ? 'selected' : '' }}>Difficulty Breathing</option>
                            <option value="Chest Pain" {{ old('concern_type') == 'Chest Pain' ? 'selected' : '' }}>Chest Pain</option>
                            <option value="Headache" {{ old('concern_type') == 'Headache' ? 'selected' : '' }}>Headache</option>
                            <option value="Nausea/Vomiting" {{ old('concern_type') == 'Nausea/Vomiting' ? 'selected' : '' }}>Nausea/Vomiting</option>
                            <option value="Diarrhea" {{ old('concern_type') == 'Diarrhea' ? 'selected' : '' }}>Diarrhea</option>
                            <option value="Injury" {{ old('concern_type') == 'Injury' ? 'selected' : '' }}>Injury</option>
                            <option value="Chronic Condition" {{ old('concern_type') == 'Chronic Condition' ? 'selected' : '' }}>Chronic Condition</option>
                            <option value="Mental Health" {{ old('concern_type') == 'Mental Health' ? 'selected' : '' }}>Mental Health</option>
                            <option value="Other" {{ old('concern_type') == 'Other' ? 'selected' : '' }}>Other (Please specify)</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the most appropriate category for your health concern</p>
                    </div>

                    <!-- Severity Level -->
                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2 text-gray-400"></i>
                            Severity Level <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                                id="severity" 
                                name="severity" 
                                required>
                            <option value="">Select severity level</option>
                            <option value="Mild" {{ old('severity') == 'Mild' ? 'selected' : '' }}>Mild - Minor symptoms, not interfering with daily activities</option>
                            <option value="Moderate" {{ old('severity') == 'Moderate' ? 'selected' : '' }}>Moderate - Some interference with daily activities</option>
                            <option value="Severe" {{ old('severity') == 'Severe' ? 'selected' : '' }}>Severe - Significant interference with daily activities</option>
                            <option value="Emergency" {{ old('severity') == 'Emergency' ? 'selected' : '' }}>Emergency - Requires immediate medical attention</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Indicate how severe your symptoms are</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-gray-400"></i>
                            Detailed Description <span class="text-red-500">*</span>
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Please describe your symptoms in detail, including when they started, how long they've been present, any triggers, and any other relevant information that might help health officials understand your situation better."
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Include specific details about symptoms, duration, and any relevant medical history</p>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-gray-400"></i>
                            Preferred Contact Number
                        </label>
                        <input type="text" 
                               id="contact_number" 
                               name="contact_number" 
                               value="{{ old('contact_number', $currentUser->contact_number ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                               placeholder="e.g., 09123456789" />
                        <p class="mt-1 text-sm text-gray-500">Provide a contact number where health officials can reach you if needed</p>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-plus mr-2 text-gray-400"></i>
                            Emergency Contact (Optional)
                        </label>
                        <input type="text" 
                               id="emergency_contact" 
                               name="emergency_contact" 
                               value="{{ old('emergency_contact') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                               placeholder="Name and contact number of emergency contact" />
                        <p class="mt-1 text-sm text-gray-500">Someone we can contact in case of emergency</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Your information will be kept confidential
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('resident.dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit Health Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Sidebar -->
        <div class="space-y-6">
            <!-- Emergency Information -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Emergency Information</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p class="mb-2">If you're experiencing a medical emergency:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Call 911 immediately</li>
                                <li>Go to the nearest hospital</li>
                                <li>Don't wait for a response here</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Health Tips</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Stay hydrated and get enough rest</li>
                                <li>Monitor your symptoms regularly</li>
                                <li>Follow any prescribed medications</li>
                                <li>Maintain good hygiene practices</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-phone text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Barangay Health Office</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p class="mb-2">For immediate assistance:</p>
                            <ul class="space-y-1">
                                <li><strong>Phone:</strong> (123) 456-7890</li>
                                <li><strong>Hours:</strong> 8:00 AM - 5:00 PM</li>
                                <li><strong>Emergency:</strong> 24/7 Hotline</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy Notice -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-gray-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-800">Privacy Notice</h3>
                        <div class="mt-2 text-sm text-gray-600">
                            <p>Your health information is protected and will only be shared with authorized health officials for the purpose of providing appropriate care and assistance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection