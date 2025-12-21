@extends('admin.main.layout')

@section('title', 'Programs for ' . $purok)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ $purok }}
                </h1>
                <p class="text-gray-600">Program recommendations for this purok</p>
            </div>
            <a href="{{ route('admin.programs.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if(empty($recommendations))
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">No program recommendations for this purok.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $rec)
                @php
                    $program = $rec['program'];
                    $stats = $rec['stats'];
                    $typeColors = [
                        'employment' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-300', 'text' => 'text-blue-700', 'icon' => 'fa-briefcase'],
                        'health' => ['bg' => 'bg-red-50', 'border' => 'border-red-300', 'text' => 'text-red-700', 'icon' => 'fa-heart'],
                        'education' => ['bg' => 'bg-green-50', 'border' => 'border-green-300', 'text' => 'text-green-700', 'icon' => 'fa-graduation-cap'],
                        'social' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-300', 'text' => 'text-yellow-700', 'icon' => 'fa-hands-helping'],
                        'safety' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-300', 'text' => 'text-purple-700', 'icon' => 'fa-shield-alt'],
                        'custom' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-300', 'text' => 'text-gray-700', 'icon' => 'fa-cog'],
                    ];
                    $colors = $typeColors[$program->type] ?? $typeColors['custom'];
                @endphp
                <div class="bg-white rounded-lg shadow-md border-l-4 {{ $colors['border'] }} overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full {{ $colors['bg'] }} {{ $colors['text'] }}">
                                    <i class="fas {{ $colors['icon'] }}"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $program->name }}</h3>
                                    <span class="text-xs {{ $colors['text'] }} uppercase">{{ $program->type }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($program->description, 80) }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Eligible:</span>
                                <span class="font-bold text-gray-900">{{ $stats['eligible_count'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Residents:</span>
                                <span class="font-bold text-gray-900">{{ $stats['total_residents'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Eligibility %:</span>
                                <span class="font-bold {{ $stats['eligibility_percentage'] >= 50 ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ number_format($stats['eligibility_percentage'], 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ min(100, $stats['eligibility_percentage']) }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.programs.show', $program->id) }}" 
                               class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

