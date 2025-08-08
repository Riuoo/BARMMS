@extends('admin.main.layout')

@section('title', 'Add Medicine')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <div class="mb-3">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Add Medicine</h1>
        <p class="text-sm md:text-base text-gray-600">Create a new medicine entry</p>
    </div>
    <form method="POST" action="{{ route('admin.medicines.store') }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <input name="name" placeholder="Name" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="generic_name" placeholder="Generic Name" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
        <select name="category" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
            @foreach(['Antibiotic','Pain Relief','Vitamins','Chronic','Emergency','Antihypertensive','Antidiabetic','Antihistamine','Other'] as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <input name="dosage_form" placeholder="Dosage Form" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="manufacturer" placeholder="Manufacturer" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="current_stock" type="number" min="0" placeholder="Initial Stock" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="minimum_stock" type="number" min="0" placeholder="Minimum Stock" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="expiry_date" type="date" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <textarea name="description" placeholder="Description (optional)" class="border border-gray-300 rounded px-3 py-2 md:col-span-2 focus:ring-green-500 focus:border-green-500"></textarea>
        <div class="md:col-span-2 flex justify-end">
            <a href="{{ route('admin.medicines.index') }}" class="px-4 py-2 mr-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
        </div>
    </form>
</div>
@endsection


