@extends('admin.layout')

@section('title', 'Resident Information')

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
        <h1 class="text-2xl font-bold mb-6">Resident Information</h1>

        <div class="mb-6 flex justify-between items-center">
            <label for="searchInput" class="block text-sm font-medium text-gray-700 sr-only">Search residents</label>
            <input
                type="text"
                id="searchInput"
                name="search"
                placeholder="Type to search residents..."
                autocomplete="off"
                class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search residents"
            />
            <a href="{{ route('admin.residents.create') }}" class="ml-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition whitespace-nowrap">Add New Resident</a>
        </div>

        <table class="min-w-full border border-gray-300 table-auto hidden sm:table">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="p-2 sm:p-3 text-left">Name</th>
                    <th class="p-2 sm:p-3 text-left">Email</th>
                    <th class="p-2 sm:p-3 text-left">Role</th>
                    <th class="p-2 sm:p-3 text-left">Address</th>
                    <th class="p-2 sm:p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="residentTableBody">
                @foreach ($residents as $resident)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="p-2 sm:p-3">{{ $resident->name }}</td>
                    <td class="p-2 sm:p-3">{{ $resident->email }}</td>
                    <td class="p-2 sm:p-3">{{ $resident->role }}</td>
                    <td class="p-2 sm:p-3">{{ $resident->address }}</td>
                    <td class="p-2 sm:p-3 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.residents.edit', $resident->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">Edit</a>
                        <form action="{{ route('admin.residents.delete', $resident->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this resident?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if ($residents->isEmpty())
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500">No residents found.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="sm:hidden space-y-4" id="mobileResidentCards">
            @foreach ($residents as $resident)
            <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition resident-card">
                <p><span class="font-semibold">Name:</span> {{ $resident->name }}</p>
                <p><span class="font-semibold">Email:</span> {{ $resident->email }}</p>
                <div class="mt-2 space-x-2">
                    <a href="{{ route('admin.residents.edit', $resident->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">Edit</a>
                    <form action="{{ route('admin.residents.delete', $resident->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this resident?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
            @if ($residents->isEmpty())
            <p class="text-center text-gray-500">No residents found.</p>
            @endif
        </div>

        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('residentTableBody');
                const mobileCardsContainer = document.getElementById('mobileResidentCards');
                const noResultsMessage = document.getElementById('noResultsMessage');

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                function filterResidents() {
                    const searchTerm = searchInput.value.trim().toLowerCase();

                    if (searchTerm === '') {
                        noResultsMessage.classList.add('hidden');
                    } 

                    let anyVisible = false;

                    // Filter table rows
                    Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                        const name = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                        const email = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                        const combined = name + ' ' + email;
                        const shouldShow = combined.includes(searchTerm);
                        row.style.display = shouldShow ? '' : 'none';
                        if (shouldShow) anyVisible = true;
                    });

                    // Filter mobile cards
                    Array.from(mobileCardsContainer.querySelectorAll('.resident-card')).forEach(card => {
                        const name = card.querySelector('p:nth-child(1)')?.textContent.toLowerCase() || '';
                        const email = card.querySelector('p:nth-child(2)')?.textContent.toLowerCase() || '';
                        const combined = name + ' ' + email;
                        const shouldShow = combined.includes(searchTerm);
                        card.style.display = shouldShow ? '' : 'none';
                        if (shouldShow) anyVisible = true;
                    });

                    if (searchTerm !== '' && !anyVisible) {
                        noResultsMessage.textContent = 'No residents match your search.';
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }
                }

                searchInput.addEventListener('input', debounce(filterResidents, 250));
            });
        </script>
    </div>
@endsection