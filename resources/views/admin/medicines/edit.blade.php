@extends('admin.main.layout')

@section('title', 'Edit Medicine')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <div class="mb-3">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Edit Medicine</h1>
        <p class="text-sm md:text-base text-gray-600">Update medicine details and stock</p>
    </div>
    <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <input name="name" value="{{ old('name', $medicine->name) }}" placeholder="Name" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" placeholder="Generic Name" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
        <select name="category" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
            @foreach(['Antibiotic','Pain Relief','Vitamins','Chronic','Emergency','Antihypertensive','Antidiabetic','Antihistamine','Other'] as $cat)
                <option value="{{ $cat }}" @selected(old('category', $medicine->category) === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
        <input name="dosage_form" value="{{ old('dosage_form', $medicine->dosage_form) }}" placeholder="Dosage Form" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" placeholder="Manufacturer" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="current_stock" type="number" min="0" value="{{ old('current_stock', $medicine->current_stock) }}" placeholder="Stock" class="border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
        <input name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $medicine->minimum_stock) }}" placeholder="Minimum Stock" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <input name="expiry_date" type="date" value="{{ old('expiry_date', optional($medicine->expiry_date)->format('Y-m-d')) }}" class="border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
        <textarea name="description" placeholder="Description (optional)" class="border border-gray-300 rounded px-3 py-2 md:col-span-2 focus:ring-green-500 focus:border-green-500">{{ old('description', $medicine->description) }}</textarea>
        <div class="md:col-span-2 flex justify-end items-center">
            <a href="{{ route('admin.medicines.index') }}" class="px-4 py-2 mr-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
        </div>
    </form>

    <!-- Restock section (separate form to avoid nesting) -->
    <div class="bg-white p-4 mt-4 rounded-lg shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Restock</h2>
        <form method="POST" action="{{ route('admin.medicines.restock', $medicine) }}" class="flex flex-wrap items-center gap-2">
            @csrf
            <input type="number" min="1" name="quantity" class="w-24 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Qty" required>
            <input type="text" name="notes" class="w-64 border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Notes (optional)">
            <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none transition">Add Stock</button>
        </form>
    </div>
</div>
@endsection