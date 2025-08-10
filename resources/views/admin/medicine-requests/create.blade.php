@extends('admin.main.layout')

@section('title', 'New Medicine Request')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <div class="mb-3">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">New Medicine Request</h1>
        <p class="text-sm md:text-base text-gray-600">Create a dispensing request for a resident</p>
    </div>

    <form method="POST" action="{{ route('admin.medicine-requests.store') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Resident</label>
            <select name="resident_id" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                @foreach($residents as $resident)
                    <option value="{{ $resident->id }}" {{ $selectedResidentId == $resident->id ? 'selected' : '' }}>{{ $resident->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Medicine</label>
            <select name="medicine_id" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                @foreach($medicines as $med)
                    <option value="{{ $med->id }}">{{ $med->name }} ({{ $med->current_stock }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Medical Record (Optional)</label>
            <select name="medical_record_id" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                <option value="">Select medical record (if applicable)</option>
                @foreach($medicalRecords as $record)
                    <option value="{{ $record->id }}" {{ $selectedMedicalRecordId == $record->id ? 'selected' : '' }}>
                        {{ $record->resident->name ?? 'Unknown' }} - {{ $record->consultation_datetime->format('M d, Y') }} 
                        ({{ $record->diagnosis ?? 'No diagnosis' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Quantity Requested</label>
            <input type="number" name="quantity_requested" min="1" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm text-gray-600 mb-1">Notes</label>
            <textarea name="notes" class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <a href="{{ route('admin.medicine-requests.index') }}" class="px-4 py-2 mr-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit</button>
        </div>
    </form>
</div>
@endsection


