@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <div onclick="location.href='{{ route('admin.blotter-reports') }}';" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Blotter Reports (This Month)</h2>
            <p class="text-6xl font-extrabold">{{ $totalBlotterReports }}</p>
            <p class="mt-4 text-lg leading-relaxed">Detailed information about blotter reports including types, dates, and statuses.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Type: Disturbance of Peace</li>
                <li>Date: 2024-06-01</li>
                <li>Status: Resolved</li>
                <li>Type: Theft</li>
                <li>Date: 2024-06-05</li>
                <li>Status: Pending</li>
            </ul>
        </div>
        <div onclick="location.href='{{ route('admin.new-account-requests') }}';" class="bg-gradient-to-r from-green-600 to-green-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Accounts Requesting Barangay Documents</h2>
            <p class="text-6xl font-extrabold">{{ $totalAccountRequests }}</p>
            <p class="mt-4 text-lg leading-relaxed">Overview of account requests for various barangay documents and their processing status.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Barangay Clearance: 15</li>
                <li>Certificate of Residency: 10</li>
                <li>Business Permit: 5</li>
                <li>Other Documents: 10</li>
            </ul>
        </div>
        <div onclick="location.href='{{ route('admin.document-requests') }}';" class="bg-gradient-to-r from-purple-600 to-purple-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Document Requests</h2>
            <p class="text-6xl font-extrabold">{{ $totalDocumentRequests }}</p>
            <p class="mt-4 text-lg leading-relaxed">Overview of document requests and their processing status.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Barangay Clearance: 12</li>
                <li>Certificate of Residency: 8</li>
                <li>Business Permit: 6</li>
                <li>Other Documents: 4</li>
            </ul>
        </div>
        <div onclick="location.href='{{ route('admin.accomplished-projects') }}';" class="bg-gradient-to-r from-yellow-600 to-yellow-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Accomplished Projects</h2>
            <p class="text-6xl font-extrabold">{{ $totalAccomplishedProjects }}</p>
            <p class="mt-4 text-lg leading-relaxed">Summary of completed projects with timelines and outcomes.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Dummy Project 1 - Completed Recently</li>
                <li>Dummy Project 2 - Completed Recently</li>
                <li>Dummy Project 3 - Completed Recently</li>
            </ul>
        </div>
        <div onclick="location.href='{{ route('admin.health-reports') }}';" class="bg-gradient-to-r from-red-600 to-red-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Health Reports</h2>
            <p class="text-6xl font-extrabold">{{ $totalHealthReports }}</p>
            <p class="mt-4 text-lg leading-relaxed">Overview of health reports and their statuses.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Dummy Health Report 1: Ongoing</li>
                <li>Dummy Health Report 2: Completed</li>
                <li>Dummy Health Report 3: Scheduled</li>
            </ul>
        </div>
    </div>
@endsection