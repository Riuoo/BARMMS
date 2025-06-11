@extends('admin.layout')

@section('title', 'Document Requests')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-6">Document Requests</h1>

    <div class="mb-6">
        <label for="documentSearchInput" class="block text-sm font-medium text-gray-700 mb-2">Search document requests</label>
        <input
            type="text"
            id="documentSearchInput"
            placeholder="Type to search by user or document type..."
            autocomplete="off"
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
            aria-label="Search document requests"
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
                <th class="p-2 sm:p-3 text-left">Document Type</th>
                <th class="p-2 sm:p-3 text-left">Description</th>
                <th class="p-2 sm:p-3 text-left">Status</th>
                <th class="p-2 sm:p-3 text-left">Created At</th>
                <th class="p-2 sm:p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody id="documentTableBody">
            @foreach($documentRequests as $request)
            <tr class="border-t border-gray-300 hover:bg-gray-100">
                <td class="p-2 sm:p-3 user-name">{{ $request->user->name ?? 'N/A' }}</td>
                <td class="p-2 sm:p-3 document-type">{{ $request->document_type }}</td>
                <td class="p-2 sm:p-3 document-description">{{ $request->description }}</td>
                <td class="p-2 sm:p-3 document-status">{{ ucfirst($request->status) }}</td>
                <td class="p-2 sm:p-3 document-created">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td class="p-2 sm:p-3 whitespace-nowrap document-actions">
                    @if($request->status === 'pending')
                    <form method="POST" action="/document-requests/{{ $request->id }}/approve" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Approve</button>
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
    <div class="sm:hidden space-y-4" id="documentMobileCards">
        @foreach($documentRequests as $request)
        <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition document-card">
            <p><span class="font-semibold">User:</span> <span class="card-user">{{ $request->user->name ?? 'N/A' }}</span></p>
            <p><span class="font-semibold">Document Type:</span> <span class="card-type">{{ $request->document_type }}</span></p>
            <p><span class="font-semibold">Description:</span> <span class="card-description">{{ $request->description }}</span></p>
            <div class="mt-2 flex space-x-4">
                @if($request->status === 'pending')
                <form method="POST" action="/document-requests/{{ $request->id }}/approve" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Approve</button>
                </form>
                @else
                <span class="text-green-600 font-semibold self-center">Approved</span>
                @endif
                <button onclick="openModal({{ $request->id }})" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">Details</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div id="documentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 ease-in-out opacity-0">
    <div class="bg-white rounded shadow-lg max-w-md w-full p-6 relative overflow-y-auto max-h-[80vh] transform transition-transform duration-300 ease-in-out scale-95" id="documentModalContent">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
        <h2 class="text-xl font-bold mb-4">Document Request Details</h2>
        <div id="modalContent">
            <!-- Document request details will be loaded here -->
        </div>
    </div>
</div>

<script>
    const documentRequests = @json($documentRequests->keyBy('id'));

    function openModal(requestId) {
        const request = documentRequests[requestId];
        if (!request) return;

        const modal = document.getElementById('documentModal');
        const modalContent = document.getElementById('modalContent');

        modalContent.innerHTML = `
            <p><strong>User:</strong> ${request.user?.name ?? 'N/A'}</p>
            <p><strong>Document Type:</strong> ${request.document_type}</p>
            <p><strong>Description:</strong> ${request.description ?? 'N/A'}</p>
            <p><strong>Status:</strong> ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</p>
            <p><strong>Created At:</strong> ${new Date(request.created_at).toLocaleString()}</p>
        `;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('flex', 'opacity-100');
            document.getElementById('documentModalContent').classList.remove('scale-95');
            document.getElementById('documentModalContent').classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('documentModal');
        modal.classList.remove('flex', 'opacity-100');
        document.getElementById('documentModalContent').classList.remove('scale-100');
        document.getElementById('documentModalContent').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('documentSearchInput');
        const tableBody = document.getElementById('documentTableBody');
        const mobileCardsContainer = document.getElementById('documentMobileCards');
        const noResultsMessage = document.getElementById('noResultsMessage');

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            }
        }

        function filterRequests() {
            const searchTerm = searchInput.value.trim().toLowerCase();

            if (searchTerm === '') {
                noResultsMessage.textContent = 'Please enter a search term to filter document requests.';
                noResultsMessage.classList.remove('hidden');
            } else {
                noResultsMessage.classList.add('hidden');
            }

            let anyVisible = false;

            // Filter table rows
            Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                const user = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const docType = row.querySelector('.document-type')?.textContent.toLowerCase() || '';
                const combined = user + ' ' + docType;
                const shouldShow = combined.includes(searchTerm);
                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) anyVisible = true;
            });

            // Filter mobile cards
            Array.from(mobileCardsContainer.querySelectorAll('.document-card')).forEach(card => {
                const user = card.querySelector('.card-user')?.textContent.toLowerCase() || '';
                const docType = card.querySelector('.card-type')?.textContent.toLowerCase() || '';
                const combined = user + ' ' + docType;
                const shouldShow = combined.includes(searchTerm);
                card.style.display = shouldShow ? '' : 'none';
                if (shouldShow) anyVisible = true;
            });

            if (searchTerm !== '' && !anyVisible) {
                noResultsMessage.textContent = 'No document requests match your search.';
                noResultsMessage.classList.remove('hidden');
            } else if (anyVisible) {
                noResultsMessage.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', debounce(filterRequests, 250));
    });
</script>
@endsection
