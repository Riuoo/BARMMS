@extends('admin.modals.layout')

@section('title', 'Create Document Request')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Create New Document Request</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.document-requests.store') }}" method="POST">
        @csrf

        <!-- Resident Search (User who is requesting the document) -->
        <div class="mb-4">
            <label for="residentSearch" class="block font-medium mb-1">Resident (Requester)</label>
            <input
                type="text"
                id="residentSearch"
                placeholder="Type to search for a resident..."
                autocomplete="off"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search for a resident"
            />
            <input type="hidden" id="user_id" name="user_id" required>
            <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded mt-1 hidden"></div>
        </div>

        <!-- Document Type -->
        <div class="mb-4">
            <label for="document_type" class="block font-medium mb-1">Document Type</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="document_type" name="document_type" required>
                <option value="">Select Document Type</option>
                <option value="Barangay Clearance">Barangay Clearance</option>
                <option value="Certificate of Residency">Certificate of Residency</option>
                <option value="Certificate of Indigency">Certificate of Indigency</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <!-- Description/Purpose -->
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description/Purpose</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="4" placeholder="e.g., For job application, school enrollment, financial assistance, etc." required>{{ old('description') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.document-requests') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit Request</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('residentSearch');
        const searchResults = document.getElementById('searchResults');
        const userIdInput = document.getElementById('user_id');

        searchInput.addEventListener('input', debounce(async () => {
            const term = searchInput.value.trim();
            if (term.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

            // Use the same search endpoint as blotter reports for residents
            const response = await fetch(`{{ route('admin.search.residents') }}?term=${term}`);
            const results = await response.json();

            if (results.length > 0) {
                searchResults.innerHTML = results.map(resident => `
                    <div class="p-2 hover:bg-gray-100 cursor-pointer" data-id="${resident.id}" data-name="${resident.name}">
                        ${resident.name} (${resident.email || 'N/A'})
                    </div>
                `).join('');
                searchResults.classList.remove('hidden');
            } else {
                searchResults.innerHTML = '<div class="p-2 text-gray-500">No residents found</div>';
                searchResults.classList.remove('hidden');
            }
        }, 250));

        searchResults.addEventListener('click', (event) => {
            if (event.target.dataset.id) {
                userIdInput.value = event.target.dataset.id;
                searchInput.value = event.target.dataset.name;
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

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }
</script>
@endsection