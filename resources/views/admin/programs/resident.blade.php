@extends('admin.main.layout')

@section('title', 'Programs for ' . $resident->full_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user mr-2"></i>
                    {{ $resident->full_name }}
                </h1>
                <p class="text-gray-600">Program recommendations for this resident</p>
            </div>
            <a href="{{ route('admin.programs.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Resident Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Resident Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Age</p>
                <p class="font-semibold text-gray-900">{{ $resident->age }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Employment Status</p>
                <p class="font-semibold text-gray-900">{{ $resident->employment_status }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Income Level</p>
                <p class="font-semibold text-gray-900">{{ $resident->income_level }}</p>
            </div>
        </div>
    </div>

    <!-- Recommended Programs -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-list-check mr-2"></i>
            Recommended Programs ({{ count($programs) }})
        </h2>
        
        @if(empty($programs))
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No program recommendations for this resident.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($programs as $program)
                    @php
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
                            
                            <div class="mt-4">
                                <a href="{{ route('admin.programs.show', $program->id) }}" 
                                   class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    View Program Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

