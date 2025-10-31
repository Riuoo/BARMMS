@extends('admin.main.layout')

@section('title', 'Decision Tree Analytics')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Decision Tree Skeleton -->
    <div id="decisionTreeSkeleton">
        @include('components.loading.decision-tree-skeleton')
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="decisionTreeContent" style="display: none;">
    <!-- Enhanced Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Decision Tree Analytics</h1>
                <p class="text-gray-600 text-lg">AI-powered classification for service eligibility and health risk assessment</p>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <button onclick="refreshAnalysis()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Analysis
                </button>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </button>
                <button onclick="exportData()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </button>
            </div>
        </div>
    </div>

    @if(isset($errors) && !empty($errors))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Analysis Errors</h3>
                    <div class="mt-2 text-sm text-red-700">
                        @foreach($errors as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Performance Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Residents Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Residents</p>
                        <p class="text-white text-3xl font-bold">{{ $sampleSize ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Eligibility Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Service Eligibility</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                                {{ round($serviceEligibility['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Risk Assessment Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Health Risk Assessment</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                                {{ round($healthRisk['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-heartbeat text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Recommendation Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Program Recommendation</p>
                        <p class="text-white text-3xl font-bold">
                            @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                                {{ round($programRecommendation['accuracy'] * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-lightbulb text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analysis Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Service Eligibility Analysis -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        Service Eligibility Analysis
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                            {{ round($serviceEligibility['accuracy'] * 100, 1) }}% Accuracy
                        @else
                            0% Accuracy
                        @endif
                    </span>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $serviceEligibility['eligible_count'] ?? 0 }}</p>
                            <p class="text-sm text-green-700">Eligible</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <p class="text-2xl font-bold text-red-600">{{ $serviceEligibility['ineligible_count'] ?? 0 }}</p>
                            <p class="text-sm text-red-700">Ineligible</p>
                        </div>
                    </div>
                    @if(isset($serviceEligibility['rules']) && !isset($serviceEligibility['error']))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-gray-900">Key Rules:</h4>
                            <div class="space-y-2">
                                @foreach($serviceEligibility['rules'] as $rule)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-700">
                                            @if(is_array($rule))
                                                {{ $rule['description'] ?? $rule['condition'] ?? 'Rule' }}
                                            @else
                                                {{ $rule }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Health Risk Assessment -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-heartbeat text-purple-600 mr-2"></i>
                        Health Risk Assessment
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                            {{ round($healthRisk['accuracy'] * 100, 1) }}% Accuracy
                        @else
                            0% Accuracy
                        @endif
                    </span>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-lg font-bold text-green-600">{{ $healthRisk['low_count'] ?? 0 }}</p>
                            <p class="text-xs text-green-700">Low Risk</p>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <p class="text-lg font-bold text-yellow-600">{{ $healthRisk['medium_count'] ?? 0 }}</p>
                            <p class="text-xs text-yellow-700">Medium Risk</p>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <p class="text-lg font-bold text-red-600">{{ $healthRisk['high_count'] ?? 0 }}</p>
                            <p class="text-xs text-red-700">High Risk</p>
                        </div>
                    </div>
                    @if(isset($healthRisk['rules']) && !isset($healthRisk['error']))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-gray-900">Risk Factors:</h4>
                            <div class="space-y-2">
                                @foreach($healthRisk['rules'] as $rule)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-700">
                                            @if(is_array($rule))
                                                {{ $rule['description'] ?? $rule['condition'] ?? 'Rule' }}
                                            @else
                                                {{ $rule }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Program Recommendation Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-lightbulb text-orange-600 mr-2"></i>
                    Program Recommendations
                </h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                        {{ round($programRecommendation['accuracy'] * 100, 1) }}% Accuracy
                    @else
                        0% Accuracy
                    @endif
                </span>
            </div>
            @if(isset($programRecommendation['recommendations']) && !isset($programRecommendation['error']))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($programRecommendation['recommendations'] as $program => $count)
                        <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $program }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $count }} residents
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">
                                @if($program === 'Financial Assistance')
                                    For residents with low income levels
                                @elseif($program === 'Health Programs')
                                    For residents with health concerns
                                @elseif($program === 'Education Support')
                                    For residents needing educational assistance
                                @elseif($program === 'Employment Training')
                                    For unemployed residents
                                @else
                                    General community support program
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p>No program recommendations available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Analysis Table -->
    @if(isset($residents) && $residents->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table text-blue-600 mr-2"></i>
                        Detailed Resident Analysis
                    </h3>
                    <div class="flex space-x-4">
                        <input type="text" id="searchTable" placeholder="Search residents..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <select id="filterCategory" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            <option value="eligible">Eligible for Services</option>
                            <option value="ineligible">Ineligible for Services</option>
                            <option value="low_risk">Low Health Risk</option>
                            <option value="medium_risk">Medium Health Risk</option>
                            <option value="high_risk">High Health Risk</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="residentsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Eligibility</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Health Risk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($residents as $resident)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $resident->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resident->age ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resident->income_level === 'High' ? 'bg-green-100 text-green-800' : ($resident->income_level === 'Low' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $resident->income_level ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resident->employment_status === 'Full-time' ? 'bg-green-100 text-green-800' : ($resident->employment_status === 'Unemployed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $resident->employment_status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $resident->health_status === 'Excellent' ? 'bg-green-100 text-green-800' : ($resident->health_status === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $resident->health_status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $eligible = ($resident->income_level === 'Low' || $resident->employment_status === 'Unemployed' || $resident->health_status === 'Critical');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $eligible ? 'Eligible' : 'Ineligible' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $risk = $resident->health_status === 'Critical' ? 'High' : ($resident->health_status === 'Good' ? 'Medium' : 'Low');
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $risk === 'Low' ? 'bg-green-100 text-green-800' : ($risk === 'High' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $risk }} Risk
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $program = '';
                                            if ($resident->income_level === 'Low') $program = 'Financial Assistance';
                                            elseif ($resident->health_status === 'Critical') $program = 'Health Programs';
                                            elseif ($resident->employment_status === 'Unemployed') $program = 'Employment Training';
                                            elseif ($resident->education_level === 'Elementary') $program = 'Education Support';
                                            else $program = 'Community Support';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $program }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button type="button" data-resident-id="{{ $resident->id }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 js-view-resident">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Insights and Recommendations -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                Analytics Insights
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-semibold text-blue-900 mb-4">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Key Performance Metrics
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-green-900">Service Eligibility Accuracy</h5>
                                    <p class="text-sm text-green-700">High accuracy in identifying eligible residents</p>
                                </div>
                                <span class="text-2xl font-bold text-green-600">
                                    @if(isset($serviceEligibility['accuracy']) && !isset($serviceEligibility['error']))
                                        {{ round($serviceEligibility['accuracy'] * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-4 border-l-4 border-purple-500 bg-purple-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-purple-900">Health Risk Assessment</h5>
                                    <p class="text-sm text-purple-700">Effective health risk classification</p>
                                </div>
                                <span class="text-2xl font-bold text-purple-600">
                                    @if(isset($healthRisk['accuracy']) && !isset($healthRisk['error']))
                                        {{ round($healthRisk['accuracy'] * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-4 border-l-4 border-orange-500 bg-orange-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="text-sm font-medium text-orange-900">Program Recommendations</h5>
                                    <p class="text-sm text-orange-700">Targeted program suggestions</p>
                                </div>
                                <span class="text-2xl font-bold text-orange-600">
                                    @if(isset($programRecommendation['accuracy']) && !isset($programRecommendation['error']))
                                        {{ round($programRecommendation['accuracy'] * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-green-900 mb-4">
                        <i class="fas fa-lightbulb text-green-600 mr-2"></i>
                        Strategic Recommendations
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-blue-900">Service Optimization</h5>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Focus on residents with low income and health concerns for immediate assistance programs.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-heartbeat text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-green-900">Health Intervention</h5>
                                    <p class="text-sm text-green-700 mt-1">
                                        Prioritize health programs for residents identified as high-risk.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-l-4 border-purple-500 bg-purple-50 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-purple-900">Education & Training</h5>
                                    <p class="text-sm text-purple-700 mt-1">
                                        Develop targeted education and employment training programs.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        // Hide consolidated skeleton
        const skeleton = document.getElementById('decisionTreeSkeleton');
        if (skeleton) skeleton.style.display = 'none';

        // Show content
        const content = document.getElementById('decisionTreeContent');
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize table search and filter
    initializeTableFeatures();
});

function initializeTableFeatures() {
    // Search functionality
    const searchInput = document.getElementById('searchTable');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#residentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Filter by category
    const filterSelect = document.getElementById('filterCategory');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const rows = document.querySelectorAll('#residentsTable tbody tr');
            
            rows.forEach(row => {
                const eligibilityCell = row.querySelector('td:nth-child(6)');
                const riskCell = row.querySelector('td:nth-child(7)');
                
                let show = true;
                if (selectedCategory) {
                    const eligibility = eligibilityCell ? eligibilityCell.textContent.trim() : '';
                    const risk = riskCell ? riskCell.textContent.trim() : '';
                    
                    if (selectedCategory === 'eligible' && !eligibility.includes('Eligible')) show = false;
                    if (selectedCategory === 'ineligible' && !eligibility.includes('Ineligible')) show = false;
                    if (selectedCategory === 'low_risk' && !risk.includes('Low')) show = false;
                    if (selectedCategory === 'medium_risk' && !risk.includes('Medium')) show = false;
                    if (selectedCategory === 'high_risk' && !risk.includes('High')) show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        });
    }
}

function refreshAnalysis() {
    location.reload();
}

function exportData() {
    // Create download link for CSV
    const link = document.createElement('a');
    link.href = '{{ route("admin.decision-tree.export") }}?format=csv';
    link.download = 'decision_tree_results.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function viewResident(residentId) {
    // Open resident details in modal or redirect
    window.open(`/admin/residents/${residentId}`, '_blank');
}
</script>

<style>
@media print {
    .btn, .flex.space-x-4 {
        display: none !important;
    }
}

@media (max-width: 768px) {
    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endsection 