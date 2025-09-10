@extends('admin.main.layout')

@section('title', 'Edit Medicine')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <!-- Header Skeleton -->
    <div id="medicineEditHeaderSkeleton">
        @include('components.loading.edit-form-skeleton', ['type' => 'header', 'showButton' => false])
    </div>

    <!-- Form Skeleton -->
    <div id="medicineEditFormSkeleton">
        @include('components.loading.edit-form-skeleton', ['type' => 'medicine'])
    </div>

    <!-- Real Content (hidden initially) -->
    <div id="medicineEditContent" style="display: none;">
        <div class="mb-2">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Medicine</h1>
            <p class="text-sm md:text-base text-gray-600">Update medicine details and stock</p>
        </div>
        
        <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Medicine Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-pills mr-2 text-green-600"></i>
                    Medicine Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Medicine Name <span class="text-red-500">*</span></label>
                        <input name="name" id="name" value="{{ old('name', $medicine->name) }}" 
                               placeholder="e.g., Paracetamol, Amoxicillin" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-sm text-gray-500">Enter the brand name or trade name of the medicine</p>
                    </div>
                    
                    <div>
                        <label for="generic_name" class="block text-sm font-medium text-gray-700 mb-2">Generic Name</label>
                        <input name="generic_name" id="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" 
                               placeholder="e.g., Acetaminophen, Amoxicillin trihydrate" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        <p class="mt-1 text-sm text-gray-500">The generic or chemical name of the medicine (optional)</p>
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                            <option value="">Select category...</option>
                            @foreach(['Antibiotic','Pain Relief','Vitamins','Chronic','Emergency','Antihypertensive','Antidiabetic','Antihistamine','Other'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $medicine->category) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select the appropriate category for this medicine</p>
                    </div>
                    
                    <div id="category_other_container" class="hidden">
                        <label for="category_other" class="block text-sm font-medium text-gray-700 mb-2">Specify Category <span class="text-red-500">*</span></label>
                        <input name="category_other" id="category_other" placeholder="e.g., Antifungal, Antiviral, etc." 
                               value="{{ old('category_other') }}"
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        <p class="mt-1 text-sm text-gray-500">Please specify the category when selecting "Other"</p>
                    </div>
                    
                    <div>
                        <label for="dosage_form" class="block text-sm font-medium text-gray-700 mb-2">Dosage Form <span class="text-red-500">*</span></label>
                        <input name="dosage_form" id="dosage_form" value="{{ old('dosage_form', $medicine->dosage_form) }}" 
                               placeholder="e.g., Tablet, Syrup, Capsule, Injection" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-sm text-gray-500">The physical form of the medicine (tablet, syrup, etc.)</p>
                    </div>
                    
                    <div>
                        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">Manufacturer <span class="text-red-500">*</span></label>
                        <input name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" 
                               placeholder="e.g., Pfizer, GSK, Sanofi" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-sm text-gray-500">The pharmaceutical company that produces this medicine</p>
                    </div>
                </div>
            </div>
            
            <!-- Stock Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-boxes mr-2 text-blue-600"></i>
                    Stock Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                        <input name="current_stock" id="current_stock" type="number" min="0" 
                               value="{{ old('current_stock', $medicine->current_stock) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                        <p class="mt-1 text-sm text-gray-500">Current stock level (use restock form below to add stock)</p>
                    </div>
                    
                    <div>
                        <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level <span class="text-red-500">*</span></label>
                        <input name="minimum_stock" id="minimum_stock" type="number" min="0" 
                               value="{{ old('minimum_stock', $medicine->minimum_stock) }}" 
                               placeholder="0" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-sm text-gray-500">Alert threshold - when stock falls below this level</p>
                    </div>
                    
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date <span class="text-red-500">*</span></label>
                        <input name="expiry_date" id="expiry_date" type="date" 
                               value="{{ old('expiry_date', optional($medicine->expiry_date)->format('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" required>
                        <p class="mt-1 text-sm text-gray-500">The expiration date of the medicine batch</p>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information Section -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                    Additional Information
                </h2>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              placeholder="Enter any additional notes about this medicine (e.g., special storage requirements, contraindications, etc.)" 
                              class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('description', $medicine->description) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Optional notes about storage, usage, or special instructions</p>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    The medicine details will be updated in the inventory.
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.medicines.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Medicine
                    </button>
                </div>
            </div>
        </form>

        <!-- Restock section (separate form to avoid nesting) -->
        <div class="bg-white p-6 mt-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-plus-circle mr-2 text-orange-600"></i>
                Add Stock
            </h2>
            <p class="text-sm text-gray-600 mb-2">Add more stock to this medicine inventory</p>
            <form method="POST" action="{{ route('admin.medicines.restock', $medicine) }}" class="flex flex-wrap items-center gap-4">
                @csrf
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="quantity" id="quantity" 
                           class="w-32 border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" 
                           placeholder="Qty" required>
                </div>
                <div class="flex-1">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <input type="text" name="notes" id="notes" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-green-500 focus:border-green-500" 
                           placeholder="e.g., New batch, donation, etc.">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const headerSkeleton = document.getElementById('medicineEditHeaderSkeleton');
        const formSkeleton = document.getElementById('medicineEditFormSkeleton');
        const content = document.getElementById('medicineEditContent');
        if (headerSkeleton) headerSkeleton.style.display = 'none';
        if (formSkeleton) formSkeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    }, 1000);
});
</script>
@endpush
@endsection