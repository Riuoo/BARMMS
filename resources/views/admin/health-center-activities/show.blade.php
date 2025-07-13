@extends('admin.modals.layout')

@section('title', 'Health Center Activity Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Health Center Activity Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.health-center-activities.edit', $healthCenterActivity->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit Activity
                </a>
                <a href="{{ route('admin.health-center-activities.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Activity Information Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                    <i class="fas fa-hospital text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $healthCenterActivity->activity_name }}</h2>
                    <p class="text-gray-600">{{ $healthCenterActivity->activity_type }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Activity Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ $healthCenterActivity->activity_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $healthCenterActivity->activity_date->format('M d, Y') }}</span>
                        </div>
                        @if($healthCenterActivity->start_time)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">
                                {{ $healthCenterActivity->start_time }}
                                @if($healthCenterActivity->end_time)
                                    - {{ $healthCenterActivity->end_time }}
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Location:</span>
                            <span class="font-medium">{{ $healthCenterActivity->location }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Status Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium px-2 py-1 rounded text-sm 
                                @if($healthCenterActivity->status == 'Completed') bg-green-100 text-green-800
                                @elseif($healthCenterActivity->status == 'In Progress') bg-blue-100 text-blue-800
                                @elseif($healthCenterActivity->status == 'Planned') bg-yellow-100 text-yellow-800
                                @elseif($healthCenterActivity->status == 'Cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $healthCenterActivity->status }}
                            </span>
                        </div>
                        @if($healthCenterActivity->organizer)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Organizer:</span>
                            <span class="font-medium">{{ $healthCenterActivity->organizer }}</span>
                        </div>
                        @endif
                        @if($healthCenterActivity->expected_participants)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expected Participants:</span>
                            <span class="font-medium">{{ $healthCenterActivity->expected_participants }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Target Information</h3>
                    <div class="space-y-2">
                        @if($healthCenterActivity->target_population)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Target Population:</span>
                            <span class="font-medium">{{ $healthCenterActivity->target_population }}</span>
                        </div>
                        @endif
                        @if($healthCenterActivity->staff_involved)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Staff Involved:</span>
                            <span class="font-medium text-sm">{{ Str::limit($healthCenterActivity->staff_involved, 30) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
            <p class="text-gray-600">{{ $healthCenterActivity->description }}</p>
        </div>

        <!-- Additional Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if($healthCenterActivity->objectives)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Objectives</h3>
                <p class="text-gray-600">{{ $healthCenterActivity->objectives }}</p>
            </div>
            @endif

            @if($healthCenterActivity->required_resources)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Resources</h3>
                <p class="text-gray-600">{{ $healthCenterActivity->required_resources }}</p>
            </div>
            @endif
        </div>

        @if($healthCenterActivity->staff_involved && !$healthCenterActivity->objectives && !$healthCenterActivity->required_resources)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Staff Involved</h3>
            <p class="text-gray-600">{{ $healthCenterActivity->staff_involved }}</p>
        </div>
        @endif

        <!-- Additional Notes -->
        @if($healthCenterActivity->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
            <p class="text-gray-600">{{ $healthCenterActivity->notes }}</p>
        </div>
        @endif

        @if(!$healthCenterActivity->objectives && !$healthCenterActivity->required_resources && !$healthCenterActivity->staff_involved && !$healthCenterActivity->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center text-gray-500">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No additional information recorded for this activity.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 