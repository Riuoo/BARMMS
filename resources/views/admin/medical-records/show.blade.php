@extends('admin.main.layout')

@section('title', 'Medical Record Entry Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Main Skeleton -->
    <div id="showMedicalSkeleton">
        @include('components.loading.show-entity-skeleton', ['type' => 'medical-record', 'buttonCount' => 2])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="showMedicalContent" style="display: none;">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Medical Record Entry Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.medical-records.index') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Records
            </a>
            <button type="button" onclick="openDeleteModal({{ $medicalRecord->id }})" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                <i class="fas fa-trash-alt mr-2"></i>Delete
            </button>
        </div>
    </div>

    <!-- Patient Information Header (aligned with vaccination show) -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-md text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-500">Record ID: #{{ $medicalRecord->id }}</div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $medicalRecord->resident?->name ?? 'N/A' }}</h2>
                    <p class="text-sm text-gray-600">{{ $medicalRecord->resident?->role ?? 'Patient' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($medicalRecord->status === 'completed') bg-green-100 text-green-800
                    @elseif($medicalRecord->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($medicalRecord->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Consultation Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Consultation Details</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ optional($medicalRecord->consultation_datetime)->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-medium">{{ optional($medicalRecord->consultation_datetime)->format('h:i A') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-medium">{{ $medicalRecord->consultation_type ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium">N/A</span>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-user text-green-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Patient Information</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium">{{ $medicalRecord->resident?->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">{{ $medicalRecord->resident?->email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Age:</span>
                    <span class="font-medium">{{ $medicalRecord->resident?->age ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chief Complaint -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">Chief Complaint</h3>
        </div>
        <p class="text-gray-700">{{ $medicalRecord->chief_complaint }}</p>
    </div>

    <!-- Vital Signs -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-heartbeat text-red-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">Vital Signs</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <div class="text-sm text-gray-600 mb-1">Blood Pressure</div>
                <div class="text-lg font-semibold">{{ $medicalRecord->blood_pressure ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Heart Rate</div>
                <div class="text-lg font-semibold">{{ $medicalRecord->heart_rate ?? 'N/A' }} bpm</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Temperature</div>
                <div class="text-lg font-semibold">{{ $medicalRecord->temperature ?? 'N/A' }}°C</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Weight</div>
                <div class="text-lg font-semibold">{{ $medicalRecord->weight ?? 'N/A' }} kg</div>
            </div>
        </div>
    </div>

    <!-- Diagnosis & Treatment -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-stethoscope text-purple-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Diagnosis</h3>
            </div>
            <p class="text-gray-700">{{ $medicalRecord->diagnosis }}</p>
    </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-pills text-green-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Treatment</h3>
            </div>
            <p class="text-gray-700">{{ $medicalRecord->treatment }}</p>
        </div>
    </div>

    <!-- Follow-up Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-calendar-check text-indigo-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">Follow-up Information</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-2">Next Appointment</div>
                <div class="font-medium">{{ $medicalRecord->follow_up_date ? $medicalRecord->follow_up_date->format('M d, Y') : 'Not scheduled' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-2">Health Worker</div>
                <div class="font-medium">{{ $medicalRecord->health_worker_name ?? 'N/A' }}</div>
                            </div>
        </div>
    </div>

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <i class="fas fa-calendar-plus text-2xl text-blue-600 mb-2"></i>
                <p class="text-sm text-gray-600">Created</p>
                <p class="font-medium">{{ optional($medicalRecord->created_at)->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div class="text-center">
                <i class="fas fa-edit text-2xl text-green-600 mb-2"></i>
                <p class="text-sm text-gray-600">Last Updated</p>
                <p class="font-medium">{{ optional($medicalRecord->updated_at)->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div class="text-center">
                <i class="fas fa-user-md text-2xl text-purple-600 mb-2"></i>
                <p class="text-sm text-gray-600">Health Worker</p>
                <p class="font-medium">{{ $medicalRecord->health_worker_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Delete Confirmation Modal (match barangay profiles) -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Delete Medical Record</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete this medical record? This will permanently remove the record from the system.</p>
        <form id="deleteForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-200">
                    Delete Record
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('showMedicalSkeleton');
        const content = document.getElementById('showMedicalContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});

function openDeleteModal(recordId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/admin/medical-records/${recordId}`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection