@extends('resident.layout')

@section('title', 'Announcements')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8">
    <h1 class="text-2xl font-bold mb-6">Barangay Announcements</h1>

    @if(empty($announcements)) {{-- You'll need to pass $announcements from the controller --}}
        <p class="text-gray-600">No announcements available at the moment.</p>
    @else
        <div class="space-y-6">
            @foreach($announcements as $announcement)
                <div class="border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $announcement->title }}</h2>
                    <p class="text-sm text-gray-500 mb-3">Published on: {{ \Carbon\Carbon::parse($announcement->created_at)->format('F d, Y h:i A') }}</p>
                    <p class="text-gray-700 leading-relaxed">{{ $announcement->content }}</p>
                    @if($announcement->attachment) {{-- Assuming an attachment field --}}
                        <div class="mt-3">
                            <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                                <i class="fas fa-paperclip mr-1"></i> View Attachment
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection