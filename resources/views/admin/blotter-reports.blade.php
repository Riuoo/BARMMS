@extends('admin.layout')

@section('title', 'Blotter Reports')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-6">Blotter Reports</h1>

    <div class="mb-6">
        <label for="blotterSearchInput" class="block text-sm font-medium text-gray-700 mb-2">Search blotter reports</label>
        <input
            type="text"
            id="blotterSearchInput"
            placeholder="Type to search by user or type..."
            autocomplete="off"
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
            aria-label="Search blotter reports"
        />
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table for larger screens -->
    <table class="min-w-full border border-gray-300 table-auto hidden sm:table">
        <thead>
            <tr class="bg-green-600 text-white">
                <th class="p-2 sm:p-3 text-left">User</th>
                <th class="p-2 sm:p-3 text-left">Type</th>
                <th class="p-2 sm:p-3 text-left">Description</th>
                <th class="p-2 sm:p-3 text-left">Media</th>
                <th class="p-2 sm:p-3 text-left">Status</th>
                <th class="p-2 sm:p-3 text-left">Created At</th>
                <th class="p-2 sm:p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody id="blotterTableBody">
            @foreach($blotterRequests as $request)
            <tr class="border-t border-gray-300 hover:bg-gray-100">
                <td class="p-2 sm:p-3 user-name">{{ $request->user->name ?? 'N/A' }}</td>
                <td class="p-2 sm:p-3 blotter-type">{{ $request->type }}</td>
                <td class="p-2 sm:p-3 blotter-description">{{ $request->description }}</td>
                <td class="p-2 sm:p-3 blotter-media">
                    @if($request->media)
                        @php
                            $extension = pathinfo($request->media, PATHINFO_EXTENSION);
                        @endphp
                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                            <img src="{{ asset('storage/' . $request->media) }}" alt="Evidence Image" class="max-w-xs max-h-32 rounded">
                        @elseif(in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv']))
                            <video controls class="max-w-xs max-h-32 rounded">
                                <source src="{{ asset('storage/' . $request->media) }}" type="video/{{ strtolower($extension) }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <a href="{{ asset('storage/' . $request->media) }}" target="_blank" class="text-blue-600 underline">View Evidence</a>
                        @endif
                    @else
                        No media
                    @endif
                </td>
                <td class="p-2 sm:p-3 blotter-status">{{ ucfirst($request->status) }}</td>
                <td class="p-2 sm:p-3 blotter-created">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td class="p-2 sm:p-3 whitespace-nowrap blotter-actions">
                    @if($request->status === 'pending')
                    <form method="POST" action="{{ route('admin.blotter-approve', $request->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Done</button>
                    </form>
                    @else
                    <span class="text-green-600 font-semibold">Approved</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p id="noResultsMessage" class="text-center text-gray-500 mt-5"></p>

    <!-- Card layout for mobile -->
    <div class="sm:hidden space-y-4" id="blotterMobileCards">
        @foreach($blotterRequests as $request)
        <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition blotter-card">
            <p><span class="font-semibold">User:</span> <span class="card-user">{{ $request->user->name ?? 'N/A' }}</span></p>
            <p><span class="font-semibold">Type:</span> <span class="card-type">{{ $request->type }}</span></p>
            <p><span class="font-semibold">Description:</span> <span class="card-description">{{ $request->description }}</span></p>
            <div class="mt-2 flex space-x-4">
                @if($request->status === 'pending')
                <form method="POST" action="{{ route('admin.blotter-approve', $request->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Done</button>
                </form>
                @else
                <span class="text-green-600 font-semibold self-center">Approved</span>
                @endif
                <button onclick="openModal({{ $request->id }})" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">Details</button>
            </div>
        </div>
        @endforeach
    </div>

<!-- Modal -->
<div id="blotterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0">
    <div class="bg-white rounded shadow-lg max-w-md w-full p-6 relative overflow-y-auto max-h-[80vh] transform transition-transform duration-300 ease-in-out scale-95" id="blotterModalContent">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
        <h2 class="text-xl font-bold mb-4">Blotter Report Details</h2>
        <div id="modalContent">
            <!-- Blotter report details will be loaded here -->
        </div>
    </div>
</div>

<script>
    const blotterRequests = @json($blotterRequests->keyBy('id'));

    function openModal(requestId) {
        const request = blotterRequests[requestId];
        if (!request) return;

        const modal = document.getElementById('blotterModal');
        const modalContent = document.getElementById('modalContent');
        let mediaContent = 'No media';
        if (request.media) {
            const extension = request.media.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
                mediaContent = `<img src="/storage/${request.media}" alt="Evidence Image" class="max-w-xs max-h-32 rounded">`;
            } else if (['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv'].includes(extension)) {
                mediaContent = `<video controls class="max-w-xs max-h-32 rounded"><source src="/storage/${request.media}" type="video/${extension}">Your browser does not support the video tag.</video>`;
            } else {
                mediaContent = `<a href="/storage/${request.media}" target="_blank" class="text-blue-600 underline">View Evidence</a>`;
            }
        }

        modalContent.innerHTML = `
            <p><strong>User:</strong> ${request.user?.name ?? 'N/A'}</p>
            <p><strong>Type:</strong> ${request.type}</p>
            <p><strong>Description:</strong> ${request.description}</p>
            <p><strong>Media:</strong> ${mediaContent}</p>
            <p><strong>Status:</strong> ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</p>
            <p><strong>Created At:</strong> ${new Date(request.created_at).toLocaleString()}</p>
        `;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('flex', 'opacity-100');
            document.getElementById('blotterModalContent').classList.remove('scale-95');
            document.getElementById('blotterModalContent').classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('blotterModal');
        modal.classList.remove('flex', 'opacity-100');
        document.getElementById('blotterModalContent').classList.remove('scale-100');
        document.getElementById('blotterModalContent').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('blotterSearchInput');
        const tableBody = document.getElementById('blotterTableBody');
        const mobileCardsContainer = document.getElementById('blotterMobileCards');
        const noResultsMessage = document.getElementById('noResultsMessage');

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            }
        }

        function filterReports() {
            const searchTerm = searchInput.value.trim().toLowerCase();

            if (searchTerm === '') {
                noResultsMessage.textContent = 'Please enter a search term to filter blotter reports.';
                noResultsMessage.classList.remove('hidden');
            } else {
                noResultsMessage.classList.add('hidden');
            }

            let anyVisible = false;

            // Filter table rows
            Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                const user = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const type = row.querySelector('.blotter-type')?.textContent.toLowerCase() || '';
                const combined = user + ' ' + type;
                const shouldShow = combined.includes(searchTerm);
                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) anyVisible = true;
            });

            // Filter mobile cards
            Array.from(mobileCardsContainer.querySelectorAll('.blotter-card')).forEach(card => {
                const user = card.querySelector('.card-user')?.textContent.toLowerCase() || '';
                const type = card.querySelector('.card-type')?.textContent.toLowerCase() || '';
                const combined = user + ' ' + type;
                const shouldShow = combined.includes(searchTerm);
                card.style.display = shouldShow ? '' : 'none';
                if (shouldShow) anyVisible = true;
            });

            if (searchTerm !== '' && !anyVisible) {
                noResultsMessage.textContent = 'No blotter reports match your search.';
                noResultsMessage.classList.remove('hidden');
            } else if (anyVisible) {
                noResultsMessage.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', debounce(filterReports, 250));
    });
</script>
</div>
@endsection
