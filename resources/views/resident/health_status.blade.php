@extends('resident.layout')

@section('title', 'Report Health Concerns')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Report Health Concerns</h1>

    <p class="mb-4 text-gray-700">Use this form to report any health-related concerns or symptoms to the barangay health officials. Your information will be kept confidential.</p>

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

    <form action="#" method="POST"> {{-- Replace # with actual route for submitting health reports --}}
        @csrf
        <div class="mb-4">
            <label for="concern_type" class="block font-medium mb-1">Type of Concern</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="concern_type" name="concern_type" required>
                <option value="">Select Concern Type</option>
                <option value="Fever">Fever</option>
                <option value="Cough/Cold">Cough/Cold</option>
                <option value="Difficulty Breathing">Difficulty Breathing</option>
                <option value="Injury">Injury</option>
                <option value="Other">Other (Please specify in description)</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Detailed Description of Concern/Symptoms</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="5" placeholder="Describe your symptoms, when they started, and any other relevant details." required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label for="contact_number" class="block font-medium mb-1">Preferred Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $currentUser->contact_number ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g., 09123456789">
        </div>

        <div class="flex justify-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Submit Health Report</button>
        </div>
    </form>
</div>
@endsection