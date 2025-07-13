@extends('admin.modals.layout')

@section('title', 'Account Requests')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Account Requests</h1>
                <p class="text-gray-600">Review and approve new account applications</p>
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

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col gap-4">
            <!-- Search Bar -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search by email..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn active px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800" data-filter="all">
                    All Requests
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="pending">
                    Pending
                </button>
                <button class="filter-btn px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="approved">
                    Approved
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-clock text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-count">{{ $accountRequests->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="pending-count">{{ $accountRequests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="approved-count">{{ $accountRequests->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="month-count">{{ $accountRequests->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table (Hidden on mobile) -->
    <div class="hidden sm:block">
        @if($accountRequests->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-clock text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No account requests found</h3>
                <p class="text-gray-500">No new account applications are waiting for approval.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Email Address
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Status
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Requested
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="accountRequestsTableBody">
                            @foreach($accountRequests as $request)
                            <tr class="account-item hover:bg-gray-50 transition duration-150" data-status="{{ $request->status }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-envelope text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $request->email }}</div>
                                            <div class="text-sm text-gray-500">New Account Request</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Approved
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ optional($request->created_at)->format('M d, Y') ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ optional($request->created_at)->diffForHumans() ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($request->status === 'pending')
                                            <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Cards (Hidden on desktop) -->
    <div class="sm:hidden space-y-4" id="accountRequestsCards">
        @if($accountRequests->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-user-clock text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No account requests found</h3>
                <p class="text-gray-500">No new account applications are waiting for approval.</p>
            </div>
        @else
            @foreach($accountRequests as $request)
            <div class="account-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200" data-status="{{ $request->status }}">
                <!-- Header Section -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-clock text-blue-600"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $request->email }}</h3>
                            <p class="text-sm text-gray-500 truncate">New Account Request</p>
                            <div class="flex items-center mt-1">
                                @if($request->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                                @elseif($request->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Approved
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Info Section -->
                <div class="mb-3">
                    <div class="items-center text-xs text-gray-500">
                        <span class="ml-2 text-xs text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ optional($request->created_at)->format('M d, Y') ?? 'N/A' }}
                        </span>
                        <span class="ml-2">
                            <i class="fas fa-clock mr-1"></i>
                            {{ optional($request->created_at)->diffForHumans() ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    @if($request->status === 'pending')
                        <form method="POST" action="{{ route('admin.account-requests.approve', $request->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-200">
                                <i class="fas fa-check mr-1"></i>
                                Approve Account
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <p id="noResultsMessage" class="text-center text-gray-500 mt-5 hidden"></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const accountItems = document.querySelectorAll('.account-item');
    const accountCards = document.querySelectorAll('.account-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterBtns.forEach(b => {
                b.classList.remove('active', 'bg-green-100', 'text-green-800');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.add('active', 'bg-green-100', 'text-green-800');
            this.classList.remove('bg-gray-100', 'text-gray-600');
            
            // Filter accounts
            const allItems = [...accountItems, ...accountCards];
            allItems.forEach(item => {
                const status = item.dataset.status;
                if (filter === 'all' || status === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            
            updateCounts();
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        const allItems = [...accountItems, ...accountCards];
        allItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Update counts
    function updateCounts() {
        const visibleItems = Array.from(accountItems).filter(item => 
            item.style.display !== 'none'
        );
        
        document.getElementById('total-count').textContent = visibleItems.length;
        document.getElementById('pending-count').textContent = visibleItems.filter(item => 
            item.dataset.status === 'pending'
        ).length;
        document.getElementById('approved-count').textContent = visibleItems.filter(item => 
            item.dataset.status === 'approved'
        ).length;
        
        const now = new Date();
        const thisMonth = visibleItems.filter(item => {
            const createdDate = new Date(item.dataset.created);
            return createdDate.getMonth() === now.getMonth() && createdDate.getFullYear() === now.getFullYear();
        }).length;
        document.getElementById('month-count').textContent = thisMonth;
    }
    
    // Initial count update
    updateCounts();
});
</script>
@endsection
