@extends('admin.main.layout')

@section('title', 'New Medicine Request')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

    <!-- Header Skeleton -->
    <div id="medRequestCreateHeaderSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'header', 'buttonCount' => 1])
    </div>

    <!-- Form Skeleton -->
    <div id="medRequestCreateFormSkeleton">
        @include('components.loading.create-form-skeleton', ['type' => 'medicine-request'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="medRequestCreateContent" style="display: none;">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-prescription-bottle-alt mr-3 text-green-600"></i>
                    New Medicine Request
                </h1>
                <p class="text-gray-600">Create a dispensing request for a resident</p>
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

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.medicine-requests.store') }}" class="space-y-6">
            @csrf

            <!-- Request Details -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-plus mr-3 text-blue-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Request Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Resident <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            id="residentSearch"
                            placeholder="Type to search for a resident..."
                            autocomplete="off"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                            aria-label="Search for a resident"
                            required
                        />
                        <input type="hidden" id="resident_id" name="resident_id" required>
                        <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        <p class="mt-1 text-sm text-gray-500">Search and select the resident for this request</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medicine <span class="text-red-500">*</span></label>
                        <select name="medicine_id" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            @foreach($medicines as $med)
                                <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->current_stock }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select the medicine to dispense</p>
                    </div>
                </div>
            </div>

            <!-- Optional Medical Record -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-notes-medical mr-3 text-purple-600 text-2xl"></i>
                    <h2 class="text-xl font-bold text-gray-900">Medical Record (Optional)</h2>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medical Record</label>
                    <select name="medical_record_id" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select medical record (if applicable)</option>
                        @foreach($medicalRecords as $record)
                            <option value="{{ $record->id }}" {{ $selectedMedicalRecordId == $record->id ? 'selected' : '' }}>
                                {{ $record->resident->name ?? 'Unknown' }} - {{ $record->consultation_datetime->format('M d, Y') }} ({{ $record->diagnosis ?? 'No diagnosis' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Link to a medical record if this request is based on a consultation</p>
                </div>
            </div>

            <!-- Quantity & Notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Requested <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity_requested" min="1" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                    <p class="mt-1 text-sm text-gray-500">Enter the quantity to dispense</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" placeholder="Any additional notes..."></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The medicine request will be created and can be managed from the requests list.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.medicine-requests.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('medRequestCreateHeaderSkeleton');
        const formSkeleton = document.getElementById('medRequestCreateFormSkeleton');
        const content = document.getElementById('medRequestCreateContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }
    const searchInput = document.getElementById('residentSearch');
    const searchResults = document.getElementById('searchResults');
    const residentIdInput = document.getElementById('resident_id');
    searchInput.addEventListener('input', debounce(async () => {
        const term = searchInput.value.trim();
        if (term.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
            return;
        }
        searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Searching…</div>';
        searchResults.classList.remove('hidden');
        try {
            const response = await fetch(`{{ route('admin.search.residents') }}?term=${encodeURIComponent(term)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const results = await response.json();
            if (Array.isArray(results) && results.length > 0) {
                searchResults.innerHTML = results.map(resident => `
                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" data-id="${resident.id}" data-name="${resident.name}">
                        <div class="font-medium text-gray-900">${resident.name}</div>
                        <div class="text-sm text-gray-500">${resident.email || 'N/A'}</div>
                    </div>
                `).join('');
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">No residents found</div>';
                searchResults.classList.remove('hidden');
            }
        } catch (e) {
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-center">Search unavailable</div>';
            searchResults.classList.remove('hidden');
        }
    }, 250));
    searchResults.addEventListener('click', (event) => {
        const target = event.target.closest('[data-id]');
        if (target && target.dataset.id) {
            residentIdInput.value = target.dataset.id;
            searchInput.value = target.dataset.name;
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });
    document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.innerHTML = '';
            searchResults.classList.add('hidden');
        }
    });
});
</script>
@endsection


