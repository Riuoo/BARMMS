@extends('admin.main.layout')

@section('title', 'Medicine Requests')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Medicine Requests</h1>
                <p class="text-sm md:text-base text-gray-600">All requests are auto-approved and dispensed</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.medicine-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    New Request
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search resident or medicine..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" />
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Filter</button>
                <a href="{{ route('admin.medicine-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Reset</a>
            </div>
        </div>
    </form>

    <!-- List -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-calendar-day mr-2 text-gray-400"></i>Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-user mr-2 text-gray-400"></i>Resident</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-pills mr-2 text-gray-400"></i>Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-sort-numeric-up mr-2 text-gray-400"></i>Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-user-md mr-2 text-gray-400"></i>Approved By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fas fa-sticky-note mr-2 text-gray-400"></i>Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $req)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4">{{ $req->request_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">{{ $req->resident->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">{{ $req->medicine->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">{{ $req->quantity_requested }}</td>
                        <td class="px-6 py-4">{{ $req->approvedByUser->name ?? 'Unknown User' }}</td>
                        <td class="px-6 py-4">
                            @if($req->notes)
                                {{ Str::limit($req->notes, 30) }}
                            @else
                                <span class="text-gray-400">No notes</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
        @foreach($requests as $req)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $req->medicine->name ?? 'Unknown' }}</h3>
                    <p class="text-xs text-gray-500">{{ $req->resident->name ?? 'Unknown' }}</p>
                </div>
            </div>
            <div class="text-sm text-gray-600 mb-2">
                <p><i class="fas fa-calendar-day mr-1 text-gray-400"></i> {{ $req->request_date->format('M d, Y') }}</p>
                <p><i class="fas fa-sort-numeric-up mr-1 text-gray-400"></i> {{ $req->quantity_requested }}</p>
                <p><i class="fas fa-user-md mr-1 text-gray-400"></i> {{ $req->approvedByUser->name ?? 'Unknown User' }}</p>
                @if($req->notes)
                    <p><i class="fas fa-sticky-note mr-1 text-gray-400"></i> {{ Str::limit($req->notes, 50) }}</p>
                @endif
            </div>
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                    <span class="text-gray-400 text-sm">—</span>
                </div>
        </div>
        @endforeach
    </div>

    @if($requests->hasPages())
    <div class="mt-4">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection


