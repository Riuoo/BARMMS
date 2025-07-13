@extends('admin.modals.layout')

@section('title', 'Patient Records')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Patient Records</h1>
        <a href="{{ route('admin.patient-records.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Add Patient Record
        </a>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, patient number..." 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
                <select id="risk_level" name="risk_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Risk Levels</option>
                    <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div>
                <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                <select id="blood_type" name="blood_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Blood Types</option>
                    <option value="A+" {{ request('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ request('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ request('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ request('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ request('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ request('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ request('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ request('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mr-2">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.patient-records.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Records</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalRecords }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">High Risk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $highRiskCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-tint text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">With Blood Type</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $withBloodTypeCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-heartbeat text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Records</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $recentRecordsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Patient Records List</h3>
        </div>
        
        @if($patientRecords->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BMI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allergies</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patientRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $record->resident->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $record->resident->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->patient_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->blood_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $record->blood_type }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->risk_level)
                                @if($record->risk_level == 'high')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        High Risk
                                    </span>
                                @elseif($record->risk_level == 'medium')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Medium Risk
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Low Risk
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">Not assessed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($record->bmi)
                                {{ $record->bmi }}
                                <span class="text-xs text-gray-500">({{ $record->getBMICategory() }})</span>
                            @else
                                <span class="text-gray-400">Not calculated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($record->allergies)
                                <span class="text-red-600">{{ Str::limit($record->allergies, 30) }}</span>
                            @else
                                <span class="text-green-600">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.patient-records.show', $record->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.patient-records.edit', $record->id) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.patient-records.destroy', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this patient record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($patientRecords->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patientRecords->links() }}
        </div>
        @endif
        @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-user-injured text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Patient Records Found</h3>
            <p class="text-gray-500 mb-4">No patient records match your search criteria.</p>
            <a href="{{ route('admin.patient-records.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Add First Patient Record
            </a>
        </div>
        @endif
    </div>
</div>
@endsection 