@extends('admin.main.layout')

@section('title', 'Due Vaccinations')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Breadcrumb Skeleton -->
    <div id="dueBreadcrumbSkeleton">
        @include('components.loading.skeleton-breadcrumb')
    </div>

    <!-- Header Skeleton -->
    <div id="dueHeaderSkeleton">
        @include('components.loading.skeleton-header-one-button')
    </div>

    <!-- Filter Skeleton -->
    <div id="dueFilterSkeleton">
        @include('components.loading.skeleton-filters')
    </div>

    <!-- Table Skeleton -->
    <div id="dueTableSkeleton" class="hidden md:block">
        @include('components.loading.skeleton-table')
    </div>

    <!-- Mobile Cards Skeleton -->
    <div id="dueMobileSkeleton" class="md:hidden">
        @include('components.loading.skeleton-mobile-cards')
    </div>

    <!-- Summary Skeleton -->
    <div id="dueSummarySkeleton">
        @include('components.loading.skeleton-due-summary')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="dueContent" style="display: none;">
    <!-- Breadcrumb Navigation -->
    <nav class="mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.vaccination-records.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-syringe mr-2"></i>
                    Vaccination Records
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">Due Vaccinations</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="mb-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Due Vaccinations</h1>
                <p class="text-sm md:text-base text-gray-600">Track vaccinations that are due soon or overdue</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.vaccination-records.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Vaccination Records
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Filter by Status</label>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.vaccination-records.due') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        All ({{ $stats['total_due'] }})
                    </a>
                    <a href="{{ route('admin.vaccination-records.due') }}?status=overdue" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        Overdue ({{ $stats['overdue'] }})
                    </a>
                    <a href="{{ route('admin.vaccination-records.due') }}?status=due_this_week" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        Due This Week ({{ $stats['due_this_week'] }})
                    </a>
                    <a href="{{ route('admin.vaccination-records.due') }}?status=due_soon" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        Due Soon ({{ $stats['due_soon'] }})
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Due Vaccinations List -->
    @if($dueVaccinations->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">All vaccinations are up to date!</h3>
            <p class="text-gray-500">No vaccinations are due soon or overdue.</p>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaccine Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Vaccination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Dose Due</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dueVaccinations as $record)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record->patient_name ?? 'Unknown Patient' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $record->patient_type ?? 'Unknown Type' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $record->vaccine_name }}</div>
                                    <div class="text-gray-500">{{ $record->vaccine_type }}</div>
                                    <div class="text-xs text-gray-400">Dose {{ $record->dose_number }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $record->vaccination_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($record->next_dose_date)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $record->next_dose_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($record->next_dose_date->isPast())
                                            Overdue
                                        @else
                                            Due in {{ $record->next_dose_date->diffForHumans() }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No follow-up</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($record->next_dose_date)
                                    @if($record->next_dose_date->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                    @elseif($record->next_dose_date->diffInDays(now()) <= 7)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Due This Week
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Due Soon
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm">No follow-up</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.vaccination-records.show', $record->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200" title="Mark as Completed">
                                        <i class="fas fa-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($dueVaccinations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $dueVaccinations->links() }}
            </div>
            @endif
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach($dueVaccinations as $record)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $record->patient_name ?? 'Unknown Patient' }}</h3>
                        <p class="text-sm text-gray-500">{{ $record->vaccine_name }}</p>
                        <div class="flex items-center mt-1">
                            @if($record->next_dose_date)
                                @if($record->next_dose_date->isPast())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @elseif($record->next_dose_date->diffInDays(now()) <= 7)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Due This Week
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Due Soon
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <p class="text-sm text-gray-600">Type: {{ $record->vaccine_type }}</p>
                    <p class="text-sm text-gray-600">Dose: {{ $record->dose_number }}</p>
                    <p class="text-sm text-gray-600">Last: {{ $record->vaccination_date->format('M d, Y') }}</p>
                    @if($record->next_dose_date)
                        <p class="text-sm text-gray-600">Next Due: {{ $record->next_dose_date->format('M d, Y') }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.vaccination-records.show', $record->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.vaccination-records.edit', $record->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                        <i class="fas fa-check mr-1"></i> Complete
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Information -->
        <div class="mt-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">
                        {{ $stats['overdue'] }}
                    </div>
                    <div class="text-sm text-gray-600">Overdue</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ $stats['due_this_week'] }}
                    </div>
                    <div class="text-sm text-gray-600">Due This Week</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $stats['due_soon'] }}
                    </div>
                    <div class="text-sm text-gray-600">Due Soon</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $stats['total_due'] }}
                    </div>
                    <div class="text-sm text-gray-600">Total Due</div>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const skeletonElements = [
            'dueBreadcrumbSkeleton', 'dueHeaderSkeleton', 'dueFilterSkeleton',
            'dueTableSkeleton', 'dueMobileSkeleton', 'dueSummarySkeleton'
        ];
        skeletonElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.style.display = 'none';
        });
        const content = document.getElementById('dueContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection
