@extends('resident.layout')

@section('title', 'Create Blotter Report')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Create New Blotter Report</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('resident.request_blotter_report') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="recipient_name" class="block font-medium mb-1">Recipient Name</label>
            <input type="text" id="recipient_name" name="recipient_name" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="type" class="block font-medium mb-1">Report Type</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="type" name="type" required>
                <option value="">Select Type</option>
                <option value="Complaint">Complaint</option>
                <option value="Incident">Incident</option>
                <option value="Dispute">Dispute</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="mb-4">
            <label for="media" class="block font-medium mb-1">Attach Evidence (Optional)</label>
            <input class="w-full border border-gray-300 rounded px-3 py-2" type="file" id="media" name="media" accept="image/*,.pdf">
            <div class="text-sm text-gray-500">Max 2MB (JPG, PNG, PDF)</div>
        </div>

        <div class="flex justify-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Blotter</button>
        </div>
    </form>
</div>
@endsection