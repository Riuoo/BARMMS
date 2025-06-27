@extends('admin.layout')

@section('title', 'Account Requests')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-6">Account Requests</h1>

    <div class="mb-6">
        <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search by Email</label>
        <input
            type="text"
            id="searchInput"
            placeholder="Type to search emails..."
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
            aria-label="Search account requests by email"
            autocomplete="off"
        />
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Table for larger screens -->
    <table class="min-w-full border border-gray-300 table-auto hidden sm:table">
        <thead>
            <tr class="bg-green-600 text-white">
                <th class="p-2 sm:p-3 text-left">Email</th>
                <th class="p-2 sm:p-3 text-left">Status</th>
                <th class="p-2 sm:p-3 text-left">Created At</th>
                <th class="p-2 sm:p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody id="accountRequestsTableBody">
            @foreach($accountRequests as $request)
            <tr class="border-t border-gray-300 hover:bg-gray-100">
                <td class="p-2 sm:p-3">{{ $request->email }}</td>
                <td class="p-2 sm:p-3">{{ ucfirst($request->status) }}</td>
                <td class="p-2 sm:p-3">{{ optional($request->created_at)->format('Y-m-d H:i') ?? 'N/A' }}</td>
                <td class="p-2 sm:p-3 whitespace-nowrap">
                    @if($request->status === 'pending')
                    <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
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
    <div class="sm:hidden space-y-4" id="accountRequestsCards">
        @foreach($accountRequests as $request)
        <div class="border border-gray-300 rounded p-4 shadow hover:shadow-lg transition">
            <p><span class="font-semibold">Email:</span> {{ $request->email }}</p>
            <p><span class="font-semibold">Status:</span> {{ ucfirst($request->status) }}</p>
            <div class="mt-2 flex space-x-4">
                @if($request->status === 'pending')
                <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Approve</button>
                </form>
                @else
                <span class="text-green-600 font-semibold self-center">Approved</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('accountRequestsTableBody');
    const cardsContainer = document.getElementById('accountRequestsCards');
    const noResultsMessage = document.getElementById('noResultsMessage');

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function filterRequests() {
        const searchTerm = searchInput.value.toLowerCase().trim();

        if (searchTerm === '') {
            noResultsMessage.classList.add('hidden');
        } else {
            noResultsMessage.classList.add('hidden');
        }

        let anyVisible = false;

        // Filter table rows
        Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
            const emailText = row.querySelector('td')?.textContent.toLowerCase() || '';
            const shouldShow = emailText.includes(searchTerm);
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) anyVisible = true;
        });

        // Filter mobile cards
        Array.from(cardsContainer.children).forEach(card => {
            const emailParagraph = card.querySelector('p > span.font-semibold');
            if (!emailParagraph) return;
            const emailTextNode = emailParagraph.nextSibling;
            const emailText = emailTextNode ? emailTextNode.textContent.trim().toLowerCase() : '';
            const shouldShow = emailText.includes(searchTerm);
            card.style.display = shouldShow ? '' : 'none';
            if (shouldShow) anyVisible = true;
        });

        if (searchTerm !== '' && !anyVisible) {
            noResultsMessage.textContent = 'No account requests match your search.';
            noResultsMessage.classList.remove('hidden');
        } else if (anyVisible) {
            noResultsMessage.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', debounce(filterRequests, 250));

    // Do NOT show any message initially
    noResultsMessage.classList.add('hidden');
});
</script>
@endsection
