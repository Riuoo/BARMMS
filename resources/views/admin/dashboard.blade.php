@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 max-w-7xl mx-auto">
        <!-- Total Residents -->
        <div onclick="location.href='{{ route('admin.residents') }}';" 
             class="tracking-card bg-teal-600 rounded-lg shadow p-6 cursor-pointer border-b-teal-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Residents</h3>
                </div>
                <div class="bg-teal-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalResidents }}</p>
        </div>

        <!-- Account Requests -->
        <div onclick="location.href='{{ route('admin.new-account-requests') }}';" 
             class="tracking-card bg-orange-600 rounded-lg shadow p-6 cursor-pointer border-b-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Account Requests</h3>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalAccountRequests }}</p>
        </div>

        <!-- Blotter Reports -->
        <div onclick="location.href='{{ route('admin.blotter-reports') }}';" 
             class="tracking-card bg-blue-600 rounded-lg shadow p-6 cursor-pointer border-b-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Blotter Reports</h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalBlotterReports }}</p>
        </div>

        <!-- Document Requests -->
        <div onclick="location.href='{{ route('admin.document-requests') }}';" 
             class="tracking-card bg-purple-600 rounded-lg shadow p-6 cursor-pointer border-b-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Document Requests</h3>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalDocumentRequests }}</p>
        </div>

        <!-- Accomplished Projects -->
        <div onclick="location.href='{{ route('admin.accomplished-projects') }}';" 
             class="tracking-card bg-yellow-600 rounded-lg shadow p-6 cursor-pointer border-b-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Accomplished Projects</h3>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalAccomplishedProjects }}</p>
        </div>

        <!-- Health Reports -->
        <div onclick="location.href='{{ route('admin.health-reports') }}';" 
             class="tracking-card bg-red-600 rounded-lg shadow p-6 cursor-pointer border-b-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white text-xl font-bold">Total Health Reports</h3>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $totalHealthReports }}</p>
        </div>
    </div>
@endsection