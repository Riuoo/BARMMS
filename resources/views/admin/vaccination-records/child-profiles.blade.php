@extends('admin.main.layout')

@section('title', 'Child Profiles')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Form Skeleton -->
    <div id="childHeaderSkeleton">
        @include('components.loading.table-dashboard-skeleton', ['showStats' => false, 'buttonCount' => 2])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="childContent" style="display: none;">
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Child Profiles</h1>
                <p class="text-sm md:text-base text-gray-600">Manage children without resident accounts</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.vaccination-records.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Vaccination Records
                </a>
                <a href="{{ route('admin.vaccination-records.create-child-profile') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Child
                </a>
            </div>
        </div>
    </div>

    <div class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="search" placeholder="Search by name or mother..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
    </div>

    <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Child</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($children as $child)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $child->first_name }} {{ $child->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $child->gender }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $child->formatted_age }}</div>
                            <div class="text-gray-500">{{ $child->age_group }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <i class="fas fa-female text-pink-500 mr-1"></i>{{ $child->mother_name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <i class="fas fa-phone text-green-500 mr-1"></i>{{ $child->contact_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>Purok {{ $child->purok }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.vaccination-records.create.child', ['child_id' => $child->id]) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Add Vaccination">
                                    <i class="fas fa-syringe"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No child profiles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($children->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $children->links() }}
        </div>
        @endif
    </div>

    <div class="md:hidden space-y-3">
        @foreach($children as $child)
        <div class="document-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center flex-1 min-w-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $child->first_name }} {{ $child->last_name }}</h3>
                        <p class="text-sm text-gray-500 truncate">{{ $child->gender }} • {{ $child->formatted_age }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.vaccination-records.create.child', ['child_id' => $child->id]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Add Vaccination">
                    <i class="fas fa-syringe mr-1"></i>
                    Add Vaccination
                </a>
            </div>
        </div>
        @endforeach
    </div>

    @if($children->hasPages())
    <div class="mt-6">
        {{ $children->links() }}
    </div>
    @endif
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeletonElements = [
            'childHeaderSkeleton', 'childSearchSkeleton', 'childTableSkeleton',
            'childMobileSkeleton', 'childPaginationSkeleton'
        ];
        skeletonElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.style.display = 'none';
        });
        const content = document.getElementById('childContent');
        if (content) content.style.display = 'block';
    }, 1000);
});

(function(){
    const input = document.getElementById('search');
    if (!input) return;
    input.addEventListener('input', function(){
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
})();
</script>
@endpush
@endsection
