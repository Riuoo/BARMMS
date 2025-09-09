@extends('admin.main.layout')

@section('title', 'Vaccination Record Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Main Skeleton -->
    <div id="vaccinationShowSkeleton">
        @include('components.loading.skeleton-vaccination-show')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="vaccinationShowContent" style="display: none;">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Vaccination Record Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.vaccination-records.index') }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <a href="{{ route('admin.vaccination-records.edit', $vaccinationRecord->id) }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                <i class="fas fa-edit mr-2"></i>Edit Record
            </a>
            <button type="button" 
                    data-vaccination-id="{{ $vaccinationRecord->id }}"
                    data-patient-name="{{ addslashes($vaccinationRecord->patient_name) }}"
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 js-delete-vaccination"
                    title="Delete Vaccination Record">
                <i class="fas fa-trash-alt mr-2"></i>Delete Record
            </button>
        </div>
    </div>

    <!-- Vaccination Information Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-syringe text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $vaccinationRecord->vaccine_name }}</h2>
                    <p class="text-sm text-gray-600">{{ $vaccinationRecord->patient_name }}</p>
                    <p class="text-sm text-gray-500">{{ $vaccinationRecord->patient_type }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($vaccinationRecord->status === 'completed') bg-green-100 text-green-800
                    @elseif($vaccinationRecord->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($vaccinationRecord->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Detailed Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Vaccine Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-vial text-blue-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Vaccine Details</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Vaccine Name:</span>
                    <span class="font-medium">{{ $vaccinationRecord->vaccine_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Manufacturer:</span>
                    <span class="font-medium">{{ $vaccinationRecord->manufacturer ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Vaccination Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                <h3 class="text-lg font-semibold text-gray-900">Vaccination Details</h3>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Vaccination Date:</span>
                    <span class="font-medium">{{ $vaccinationRecord->vaccination_date ? $vaccinationRecord->vaccination_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Next Due Date:</span>
                    <span class="font-medium">{{ $vaccinationRecord->next_due_date ? $vaccinationRecord->next_due_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Dose Number:</span>
                    <span class="font-medium">{{ $vaccinationRecord->dose_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-user text-purple-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">Patient Information</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-sm text-gray-600">Patient Name</label>
                <p class="font-medium">{{ $vaccinationRecord->patient_name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Patient Type</label>
                <p class="font-medium">{{ ucfirst($vaccinationRecord->patient_type) }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600">Age</label>
                <p class="font-medium">{{ $vaccinationRecord->age ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Record Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <i class="fas fa-calendar-plus text-2xl text-blue-600 mb-2"></i>
                <p class="text-sm text-gray-600">Created</p>
                <p class="font-medium">{{ $vaccinationRecord->created_at->format('M d, Y') }}</p>
            </div>
            <div class="text-center">
                <i class="fas fa-edit text-2xl text-green-600 mb-2"></i>
                <p class="text-sm text-gray-600">Last Updated</p>
                <p class="font-medium">{{ $vaccinationRecord->updated_at->format('M d, Y') }}</p>
            </div>
            <div class="text-center">
                <i class="fas fa-user-md text-2xl text-purple-600 mb-2"></i>
                <p class="text-sm text-gray-600">Health Worker</p>
                <p class="font-medium">{{ $vaccinationRecord->health_worker_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Delete Vaccination Record</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete the vaccination record for <span id="patientName" class="font-semibold"></span>? This will permanently remove the record from the system.</p>
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
function deleteVaccination(id, name) {
    document.getElementById('patientName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/vaccination-records/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Delegated handler for delete button
document.addEventListener('click', function (event) {
    const deleteBtn = event.target.closest('.js-delete-vaccination');
    if (deleteBtn) {
        const id = deleteBtn.getAttribute('data-vaccination-id');
        const name = deleteBtn.getAttribute('data-patient-name');
        if (id) deleteVaccination(id, name);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeleton = document.getElementById('vaccinationShowSkeleton');
        const content = document.getElementById('vaccinationShowContent');
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection