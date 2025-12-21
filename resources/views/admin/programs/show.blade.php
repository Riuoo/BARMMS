@extends('admin.main.layout')

@section('title', $program->name . ' - Program Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-project-diagram mr-2"></i>
                    {{ $program->name }}
                </h1>
                <p class="text-gray-600">{{ $program->description }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.programs.export', $program->id) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
                <a href="{{ route('admin.programs.index') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Program Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Program Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="font-semibold text-gray-900 capitalize">{{ $program->type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Priority</p>
                <p class="font-semibold text-gray-900">{{ $program->priority }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="px-2 py-1 rounded {{ $program->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $program->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Eligible Residents</p>
                <p class="font-semibold text-gray-900">{{ array_sum(array_column($recommendations, 'eligible_count')) }}</p>
            </div>
        </div>
    </div>

    <!-- Purok Groups -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-map-marker-alt mr-2"></i>
            Recommendations by Purok
        </h2>
        
        @if(empty($recommendations))
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No eligible residents found for this program.</p>
            </div>
        @else
            @foreach($recommendations as $rec)
                <div class="bg-white rounded-lg shadow-md mb-4 border-l-4 {{ $rec['is_recommended'] ? 'border-green-500' : 'border-gray-300' }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $rec['purok'] }}</h3>
                                @if($rec['is_recommended'])
                                    <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Recommended
                                    </span>
                                @else
                                    <span class="ml-3 px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                        Not Recommended
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Total Residents</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $rec['total_residents'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Eligible Count</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $rec['eligible_count'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Eligibility %</p>
                                <p class="text-2xl font-bold {{ $rec['eligibility_percentage'] >= 50 ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ number_format($rec['eligibility_percentage'], 1) }}%
                                </p>
                            </div>
                            <div>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-blue-600 h-4 rounded-full" 
                                         style="width: {{ min(100, $rec['eligibility_percentage']) }}%"></div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($rec['eligible_residents']))
                            <div class="mt-4">
                                <button onclick="toggleResidents('{{ $rec['purok_token'] }}')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    <i class="fas fa-chevron-down mr-1" id="icon-{{ $rec['purok_token'] }}"></i>
                                    View Eligible Residents ({{ count($rec['eligible_residents']) }})
                                </button>
                                
                                <div id="residents-{{ $rec['purok_token'] }}" class="hidden mt-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employment</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Income</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($rec['eligible_residents'] as $resident)
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $resident->full_name }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->age }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->employment_status }}</td>
                                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $resident->income_level }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
function toggleResidents(purokToken) {
    const element = document.getElementById('residents-' + purokToken);
    const icon = document.getElementById('icon-' + purokToken);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        element.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endsection

