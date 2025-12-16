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
                        <label for="name" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Medicine Name <span class="text-red-500">*</span></label>
                        <input name="name" id="name" value="{{ old('name', $medicine->name) }}" 
                               placeholder="Example: Paracetamol, Amoxicillin" 
                               class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed" readonly required>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Enter the brand name or trade name of the medicine</p>
                    </div>
                    
                    <div>
                        <label for="generic_name" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Generic Name</label>
                        <input name="generic_name" id="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" 
                               placeholder="Example: Acetaminophen, Amoxicillin trihydrate" 
                               class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed" readonly>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">The generic or chemical name of the medicine (optional)</p>
                    </div>
                    
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select id="category" class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 cursor-not-allowed" disabled>
                            <option value="">Select category...</option>
                            @foreach(['Antibiotic','Pain Relief','Vitamins','Chronic','Emergency','Antihypertensive','Antidiabetic','Antihistamine','Other'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $medicine->category) === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        {{-- Preserve value for validation/update while keeping field visually disabled --}}
                        <input type="hidden" name="category" value="{{ old('category', $medicine->category) }}">
                        <p class="mt-1 text-sm text-gray-500">Select the appropriate category for this medicine</p>
                    </div>
                    
                    <div id="category_other_container" class="hidden">
                        <label for="category_other" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Specify Category <span class="text-red-500">*</span></label>
                        <input name="category_other" id="category_other" placeholder="Example: Antifungal, Antiviral" 
                               value="{{ old('category_other') }}"
                               class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed" readonly>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Please specify the category when selecting "Other"</p>
                    </div>
                    
                    <div>
                        <label for="dosage_form" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Dosage Form <span class="text-red-500">*</span></label>
                        <input name="dosage_form" id="dosage_form" value="{{ old('dosage_form', $medicine->dosage_form) }}" 
                               placeholder="Example: Tablet, Syrup, Capsule, Injection" 
                               class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed" readonly required>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">The physical form of the medicine (tablet, syrup, etc.)</p>
                    </div>
                    
                    <div>
                        <label for="manufacturer" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Manufacturer <span class="text-red-500">*</span></label>
                        <input name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" 
                               placeholder="Example: Pfizer, GSK, Sanofi" 
                               class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed" readonly required>
                        <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">The pharmaceutical company that produces this medicine</p>
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
                    <label for="description" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              placeholder="Enter notes (Example: Special storage requirements, contraindications)" 
                              class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400 cursor-not-allowed resize-none"
                              readonly>{{ old('description', $medicine->description) }}</textarea>
                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Optional notes about storage, usage, or special instructions</p>
                </div>
            </div>

            <!-- Batch Information Section -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-boxes mr-2 text-blue-600"></i>
                    Batches & Expiry
                </h2>
                @if(isset($batches) && $batches->isNotEmpty())
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Batch ID</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batches as $batch)
                                    @php
                                        $status = 'No expiry';
                                        $statusClass = 'text-gray-600';
                                        if ($batch->expiry_date) {
                                            $days = now()->diffInDays($batch->expiry_date, false);
                                            if ($days < 0) {
                                                $status = 'Expired';
                                                $statusClass = 'text-red-600 font-semibold';
                                            } elseif ($days <= 30) {
                                                $status = 'Expiring soon';
                                                $statusClass = 'text-yellow-600 font-semibold';
                                            } else {
                                                $status = 'Valid';
                                                $statusClass = 'text-green-600 font-semibold';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 text-gray-700">#{{ $batch->id }}</td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $batch->expiry_date ? $batch->expiry_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">{{ $batch->quantity }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $batch->remaining_quantity }}</td>
                                        <td class="px-4 py-2">
                                            <span class="{{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No batch information available yet for this medicine.</p>
                @endif
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
                    <label for="quantity" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="quantity" id="quantity" 
                           class="w-32 border border-gray-600 dark:border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400" 
                           placeholder="Qty" required>
                </div>
                <div>
                    <label for="restock_expiry_date" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-1">Expiry Date <span class="text-red-500">*</span></label>
                    <input type="date" name="restock_expiry_date" id="restock_expiry_date"
                           class="w-48 border border-gray-600 dark:border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900"
                           required>
                </div>
                <div class="flex-1">
                    <label for="notes" class="block text-sm font-medium text-gray-200 dark:text-gray-700 mb-1">Notes (optional)</label>
                    <input type="text" name="notes" id="notes" 
                           class="w-full border border-gray-600 dark:border-gray-300 rounded px-3 py-2 focus:ring-green-400 focus:border-green-400 bg-gray-800 dark:bg-white text-gray-100 dark:text-gray-900 placeholder-gray-400 dark:placeholder-gray-400" 
                           placeholder="Example: New batch, donation">
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