@extends('admin.modals.layout')

@section('title', 'Vaccination Records')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Vaccination Records</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.vaccination-records.due') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                <i class="fas fa-clock mr-2"></i>Due Vaccinations
            </a>
            <a href="{{ route('admin.vaccination-records.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Add Vaccination
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.vaccination-records.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="query" value="{{ $query ?? '' }}" 
                       placeholder="Search by patient name, vaccine name, or type..." 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </form>
    </div>

    <!-- Vaccination Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vaccine Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vaccination Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Next Dose
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vaccinationRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-syringe text-green-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $record->resident->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $record->resident->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $record->vaccine_name }}</div>
                                <div class="text-gray-500">{{ $record->vaccine_type }}</div>
                                <div class="text-xs text-gray-400">
                                    Dose {{ $record->dose_number }}
                                    @if($record->manufacturer)
                                        • {{ $record->manufacturer }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $record->vaccination_date->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $record->administered_by ?? 'Not specified' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->next_dose_date)
                                @if($record->next_dose_date->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @elseif($record->next_dose_date->diffInDays(now()) <= 30)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Due Soon
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Scheduled
                                    </span>
                                @endif
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $record->next_dose_date->format('M d, Y') }}
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">No follow-up</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.vaccination-records.show', $record->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.vaccination-records.destroy', $record->id) }}" 
                                      method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this vaccination record?')">
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
                                <i class="fas fa-syringe text-4xl text-gray-300 mb-2"></i>
                                <p>No vaccination records found.</p>
                                <a href="{{ route('admin.vaccination-records.create') }}" class="text-blue-600 hover:text-blue-800 mt-2">
                                    Add your first vaccination record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($vaccinationRecords->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $vaccinationRecords->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 