@extends('admin.layout')

@section('title', 'Barangay Profiles')

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
        <h1 class="text-2xl font-bold mb-6">Barangay Profiles</h1>

        <div class="mb-6 flex justify-between items-center">
            <label for="searchInput" class="block text-sm font-medium text-gray-700 sr-only">Search users</label>
            <input
                type="text"
                id="searchInput"
                name="search"
                placeholder="Type to search users..."
                autocomplete="off"
                class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search barangay profiles"
            />
            <a href="{{ route('admin.barangay-profiles.create') }}" class="ml-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i>Add New Profile
            </a>
        </div>

        <table class="min-w-full border border-gray-300 table-auto hidden sm:table">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Name</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Email</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Role</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Address</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @foreach ($barangayProfiles as $user)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $user->name }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $user->email }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $user->role }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $user->address }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 space-x-2 whitespace-nowrap text-center">
                        <a href="{{ route('admin.barangay-profiles.edit', $user->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.barangay-profiles.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if ($barangayProfiles->isEmpty())
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500">No active users found.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="sm:hidden space-y-4" id="mobileUser Cards">
            @foreach ($barangayProfiles as $user)
            <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition user-card">
                <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                <div class="mt-2 space-x-2">
                    <a href="{{ route('admin.barangay-profiles.edit', $user->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">Edit</a>
                    <form action="{{ route('admin.barangay-profiles.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
            @if ($barangayProfiles->isEmpty())
            <p class="text-center text-gray-500">No active users found.</p>
            @endif
        </div>

        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('searchInput');
                const tableBody = document.getElementById('userTableBody');
                const mobileCardsContainer = document.getElementById('mobileUser Cards');
                const noResultsMessage = document.getElementById('noResultsMessage');

                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                function filterUsers() {
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
                    Array.from(mobileCardsContainer.querySelectorAll('.user-card')).forEach(card => {
                        const name = card.querySelector('p:nth-child(1)')?.textContent.toLowerCase() || '';
                        const email = card.querySelector('p:nth-child(2)')?.textContent.toLowerCase() || '';
                        const combined = name + ' ' + email;
                        const shouldShow = combined.includes(searchTerm);
                        card.style.display = shouldShow ? '' : 'none';
                        if (shouldShow) anyVisible = true;
                    });

                    if (searchTerm !== '' && !anyVisible) {
                        noResultsMessage.textContent = 'No users match your search.';
                        noResultsMessage.classList.remove('hidden');
                    } else {
                        noResultsMessage.classList.add('hidden');
                    }
                }

                searchInput.addEventListener('input', debounce(filterUsers, 250));
            });
        </script>
    </div>
@endsection