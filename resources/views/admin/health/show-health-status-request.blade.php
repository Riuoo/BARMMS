@extends('admin.main.layout')

@section('title', 'Health Status Request Details')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Health Status Request #{{ $healthRequest->id }}</h1>
                <p class="text-gray-600">Review and manage this health concern from {{ $healthRequest->user->name ?? 'Unknown Resident' }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.health-status-requests') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Requests
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
        <!-- Request Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Resident Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Resident Information</h3>
                        <p class="text-sm text-gray-500">Details about the resident who submitted this request</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <p class="text-sm text-gray-900">{{ $healthRequest->user->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-sm text-gray-900">{{ $healthRequest->user->email ?? 'No email' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <p class="text-sm text-gray-900">{{ $healthRequest->contact_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                        <p class="text-sm text-gray-900">{{ $healthRequest->emergency_contact ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Health Concern Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-heartbeat text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Health Concern Details</h3>
                        <p class="text-sm text-gray-500">Information about the health concern</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concern Type</label>
                        <p class="text-sm text-gray-900">{{ $healthRequest->concern_type }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                        @php
                            $severityColors = [
                                'Mild' => 'bg-green-100 text-green-800',
                                'Moderate' => 'bg-yellow-100 text-yellow-800',
                                'Severe' => 'bg-orange-100 text-orange-800',
                                'Emergency' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $severityColors[$healthRequest->severity] }}">
                            {{ $healthRequest->severity }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $healthRequest->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Request Timeline</h3>
                        <p class="text-sm text-gray-500">Important dates and status updates</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                            <p class="text-sm text-gray-500">{{ $healthRequest->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($healthRequest->reviewed_at)
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Reviewed</p>
                                <p class="text-sm text-gray-500">{{ $healthRequest->reviewed_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Management -->
        <div class="space-y-6">
            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-flag text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Current Status</h3>
                        <p class="text-sm text-gray-500">Manage the request status</p>
                    </div>
                </div>
                
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'reviewed' => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-orange-100 text-orange-800',
                        'resolved' => 'bg-green-100 text-green-800'
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$healthRequest->status] }}">
                    {{ ucfirst(str_replace('_', ' ', $healthRequest->status)) }}
                </span>
            </div>

            <!-- Update Status Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
                
                <form action="{{ route('admin.health-status-requests.update-status', $healthRequest->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="pending" {{ $healthRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $healthRequest->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="in_progress" {{ $healthRequest->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $healthRequest->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                        <textarea name="admin_notes" id="admin_notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Add notes about this health concern...">{{ $healthRequest->admin_notes }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Optional notes for internal reference</p>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="mailto:{{ $healthRequest->user->email ?? '' }}?subject=Health Status Request #{{ $healthRequest->id }}" 
                       class="flex items-center w-full px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200">
                        <i class="fas fa-envelope mr-3"></i>
                        Contact Resident
                    </a>
                    
                    @if($healthRequest->contact_number)
                        <a href="tel:{{ $healthRequest->contact_number }}" 
                           class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50 rounded-lg transition duration-200">
                            <i class="fas fa-phone mr-3"></i>
                            Call Resident
                        </a>
                    @endif
                    
                    <button onclick="window.print()" 
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition duration-200">
                        <i class="fas fa-print mr-3"></i>
                        Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 