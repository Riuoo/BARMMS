@extends('admin.layout')

@section('title', 'Active Users')

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
        <h1 class="text-2xl font-bold mb-6">Active Users</h1>

        <div class="mb-6">
            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search users</label>
            <input
                type="text"
                id="searchInput"
                name="search"
                placeholder="Type to search users..."
                autocomplete="off"
                class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                aria-label="Search active users"
            />
        </div>

        <!-- Table for larger screens -->
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
            <tbody id="userTableBody">
                @foreach ($users as $user)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="p-2 sm:p-3">{{ $user->name }}</td>
                    <td class="p-2 sm:p-3">{{ $user->email }}</td>
                    <td class="p-2 sm:p-3">{{ $user->role }}</td>
                    <td class="p-2 sm:p-3">{{ $user->address }}</td>
                    <td class="p-2 sm:p-3 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">Edit</a>
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if ($users->isEmpty())
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500">No active users found.</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Card layout for mobile -->
        <div class="sm:hidden space-y-4" id="mobileUserCards">
            @foreach ($users as $user)
            <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition user-card">
                <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                <div class="mt-2 space-x-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700 transition">Edit</a>
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700 transition">Delete</button>
                    </form>
                    <button onclick="openModal({{ $user->id }})" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">Details</button>
                </div>
            </div>
            @endforeach
            @if ($users->isEmpty())
            <p class="text-center text-gray-500">No active users found.</p>
            @endif
        </div>

        <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>

    <!-- Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0">
        <div class="bg-white rounded shadow-lg max-w-md w-full p-6 relative overflow-y-auto max-h-[80vh] transform transition-transform duration-300 ease-in-out scale-95" id="userModalContent">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
            <h2 class="text-xl font-bold mb-4">User Details</h2>
            <div id="modalContent">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('userTableBody');
            const mobileCardsContainer = document.getElementById('mobileUserCards');
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

                if(searchTerm === '') {
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

                if(searchTerm !== '' && !anyVisible) {
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