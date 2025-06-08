@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <div onclick="location.href='{{ url('/blotter-reports') }}';" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Blotter Reports (This Month)</h2>
            <p class="text-6xl font-extrabold">{{ $totalBlotterReports ?? 25 }}</p>
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
        <div onclick="location.href='{{ url('/account-requests') }}';" class="bg-gradient-to-r from-green-600 to-green-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Accounts Requesting Barangay Documents</h2>
            <p class="text-6xl font-extrabold">{{ $totalAccountRequests ?? 40 }}</p>
            <p class="mt-4 text-lg leading-relaxed">Overview of account requests for various barangay documents and their processing status.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Barangay Clearance: 15</li>
                <li>Certificate of Residency: 10</li>
                <li>Business Permit: 5</li>
                <li>Other Documents: 10</li>
            </ul>
        </div>
        <div onclick="location.href='{{ url('/accomplished-projects') }}';" class="bg-gradient-to-r from-yellow-600 to-yellow-800 text-white rounded-xl shadow-xl p-10 cursor-pointer hover:shadow-3xl transition transform hover:-translate-y-3 hover:scale-105">
            <h2 class="text-2xl font-bold mb-6 tracking-wide">Total Accomplished Projects</h2>
            <p class="text-6xl font-extrabold">{{ $totalAccomplishedProjects ?? 15 }}</p>
            <p class="mt-4 text-lg leading-relaxed">Summary of completed projects with timelines and outcomes.</p>
            <ul class="list-disc list-inside mt-6 space-y-1 text-sm font-medium">
                <li>Road Improvement Project - Completed May 2024</li>
                <li>Community Health Program - Completed April 2024</li>
                <li>Waste Management Initiative - Completed March 2024</li>
            </ul>
        </div>
    </div>
@endsection
