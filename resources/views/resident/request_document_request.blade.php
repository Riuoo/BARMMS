@extends('resident.layout')

@section('title', 'Request New Document')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Request New Document</h1>

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

    <form action="{{ route('resident.request_document_request') }}" method="POST">
        @csrf

        <!-- Document Type -->
        <div class="mb-4">
            <label for="document_type" class="block font-medium mb-1">Document Type</label>
            <select class="w-full border border-gray-300 rounded px-3 py-2" id="document_type" name="document_type" required>
                <option value="">Select Document Type</option>
                <option value="Barangay Clearance">Barangay Clearance</option>
                <option value="Certificate of Residency">Certificate of Residency</option>
                <option value="Certificate of Indigency">Certificate of Indigency</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <!-- Description/Purpose -->
        <div class="mb-4">
            <label for="description" class="block font-medium mb-1">Description/Purpose</label>
            <textarea class="w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" rows="4" placeholder="e.g., For job application, school enrollment, financial assistance, etc." required>{{ old('description') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit Request</button>
        </div>
    </form>
</div>
@endsection