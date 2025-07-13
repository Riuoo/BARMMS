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

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.patient-records.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="query" value="{{ $query ?? '' }}" 
                       placeholder="Search by patient name, email, or patient number..." 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </form>
    </div>

    <!-- Patient Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Health Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Risk Level
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Emergency Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patientRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user-md text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $record->resident->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $record->patient_number }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $record->resident->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center space-x-4">
                                    @if($record->blood_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $record->blood_type }}
                                    </span>
                                    @endif
                                    @if($record->bmi)
                                    <span class="text-gray-600">BMI: {{ $record->bmi }}</span>
                                    @endif
                                </div>
                                @if($record->allergies)
                                <div class="text-xs text-gray-500 mt-1">
                                    Allergies: {{ Str::limit($record->allergies, 50) }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $riskColors = [
                                    'Low' => 'bg-green-100 text-green-800',
                                    'Medium' => 'bg-yellow-100 text-yellow-800',
                                    'High' => 'bg-orange-100 text-orange-800',
                                    'Critical' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $riskColors[$record->risk_level] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $record->risk_level }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($record->emergency_contact_name)
                            <div class="text-sm text-gray-900">
                                {{ $record->emergency_contact_name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $record->emergency_contact_number }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $record->emergency_contact_relationship }}
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">Not provided</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.patient-records.show', $record->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.patient-records.edit', $record->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.patient-records.destroy', $record->id) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this patient record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-md text-4xl text-gray-300 mb-2"></i>
                                <p>No patient records found.</p>
                                <a href="{{ route('admin.patient-records.create') }}" class="text-blue-600 hover:text-blue-800 mt-2">
                                    Add your first patient record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($patientRecords->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $patientRecords->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 