@extends('admin.main.layout')

@section('title', 'Child Profiles')

@section('content')
<div class="max-w-6xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-child text-blue-600"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Child Profiles</h1>
                <p class="text-sm text-gray-600">Manage children without resident accounts</p>
            </div>
        </div>
        <a href="{{ route('admin.vaccination-records.create-child-profile') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
            <i class="fas fa-plus mr-2"></i>
            Add New Child
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="search" placeholder="Search by name or mother..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Child</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($children as $child)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $child->formatted_age }}</div>
                                <div class="text-gray-500">{{ $child->age_group }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <i class="fas fa-female text-pink-500 mr-1"></i>{{ $child->mother_name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <i class="fas fa-phone text-green-500 mr-1"></i>{{ $child->contact_number ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>Purok {{ $child->purok }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="#" class="inline-flex items-center px-2 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="#" class="inline-flex items-center px-2 py-1.5 ml-2 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-syringe mr-1"></i> Add Vaccination
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No child profiles found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($children->hasPages())
            <div class="mt-6">
                {{ $children->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
