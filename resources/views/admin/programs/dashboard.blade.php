@extends('admin.main.layout')

@section('title', 'Program Recommendations')

@php
    $userRole = session('user_role');
    $isSecretary = $userRole === 'secretary';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-project-diagram mr-2"></i>
                    Program Recommendations
                </h1>
                <p class="text-gray-600">
                    Programs recommended for residents based on demographics, blotter reports, and medical records
                </p>
            </div>
            <!-- @if($isSecretary)
                <div>
                    <a href="{{ route('admin.programs.manage.index') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-cog mr-2"></i>Manage Programs
                    </a>
                </div>
            @endif -->
        </div>
    </div>

    @if(isset($error))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ $error }}</p>
                </div>
            </div>
        </div>
    @else

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-list text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Programs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $programs->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Eligible</p>
                    <p class="text-2xl font-bold text-gray-900">{{ array_sum(array_column($programStats, 'total_eligible')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Target Puroks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ array_sum(array_column($programStats, 'target_puroks_count')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Active Programs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $programs->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($programStats as $stat)
            @php
                $program = $stat['program'];
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
                    
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($program->description, 100) }}</p>
                    
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <span class="text-gray-600">Eligible:</span>
                            <span class="font-bold text-gray-900">{{ $stat['total_eligible'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Puroks:</span>
                            <span class="font-bold text-gray-900">{{ $stat['target_puroks_count'] }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.programs.show', $program->id) }}" 
                           class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($programs->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">No programs available yet.</p>
        </div>
    @endif
    @endif
</div>
@endsection

