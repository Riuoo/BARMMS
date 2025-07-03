{{-- resources/views/admin/create_blotter_report.blade.php --}}

@extends('admin.layout')

@section('title', 'Create Blotter Reports')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Create New Blotter Report</h1>

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

    <form action="{{ route('admin.blotter-reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Complainant Search -->
        <div class="mb-4">
            <label for="complainantSearch" class="block font-medium mb-1">Complainant</label>
            <input
                type="text"
                id="complainantSearch"
                placeholder="Type to search for a resident..."
                autocomplete="off"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search for a resident"
            />
            <input type="hidden" id="resident_id" name="resident_id" required>
            <div id="searchResults" class="absolute z-10 bg-white border border-gray-300 rounded mt-1 hidden"></div>
        </div>

        <!-- Recipient Name -->
        <div class="mb-4">
            <label for="recipient_name" class="block font-medium mb-1">Recipient Name (Complainant's Enemy)</label>
            <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <!-- Report Type -->
        <div class="mb-4">
            <label for="type" class="block font-medium mb-1">Report Type</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="Complaint">Complaint</option>
                <option value="Incident">Incident</option>
                <option value="Dispute">Dispute</option>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="4" required></textarea>
        </div>

        <!-- File Upload -->
        <div class="mb-4">
            <label for="media" class="block font-medium mb-1">Attach Evidence (Optional)</label>
            <input class="w-full border border-gray-300 rounded px-3 py-2" type="file" id="media" name="media" accept="image/*,.pdf">
            <div class="text-sm text-gray-500">Max 2MB (JPG, PNG, PDF)</div>
        </div>

        <!-- Add this inside your form, for example, after the Description field -->
        <div class="mb-4">
            <label for="summon_date" class="block font-medium mb-1">Summon Date</label>
            <input type="datetime-local" id="summon_date" name="summon_date"
                min="{{ now()->format('Y-m-d\TH:i') }}"
                class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.blotter-reports') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Back to Blotter Reports</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Blotter</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('complainantSearch');
        const searchResults = document.getElementById('searchResults');
        const residentIdInput = document.getElementById('resident_id');

        searchInput.addEventListener('input', debounce(async () => {
            const term = searchInput.value.trim();
            if (term.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

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
                searchResults.innerHTML = '<div class="p-2 text-gray-500">No results found</div>';
                searchResults.classList.remove('hidden');
            }
        }, 250));

        searchResults.addEventListener('click', (event) => {
            if (event.target.dataset.id) {
                residentIdInput.value = event.target.dataset.id;
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