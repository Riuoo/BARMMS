@extends('admin.main.layout')

@section('title', 'FAQ Management')
@section('content')
<div class="max-w-6xl mx-auto pt-2">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">FAQ Management</h1>
        <a href="{{ route('admin.faqs.create') }}" class="bg-green-600 text-white rounded-lg px-5 py-2 hover:bg-green-700 font-semibold flex items-center"><i class="fas fa-plus mr-2"></i>Add FAQ</a>
    </div>
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    <form method="GET" class="mb-3 flex flex-wrap gap-2 items-center">
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
        <select name="category" class="px-3 py-2 border border-gray-300 rounded">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" @if(request('category') === $cat) selected @endif>{{ $cat }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>
    @if($faqs->count() == 0)
        <div class="py-12 text-center text-gray-500">No FAQs found.</div>
    @else
    <form id="faqOrderForm">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border rounded shadow-sm bg-white" id="faqTable">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="w-7"></th>
                    <th>Order</th>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id="faqTableBody">
                @foreach($faqs as $faq)
                <tr data-id="{{ $faq->id }}" class="group hover:bg-green-50">
                    <td class="cursor-grab text-gray-400 text-lg select-none sort-handle"><i class="fas fa-bars"></i></td>
                    <td class="py-1 px-2 text-center">{{ $faq->order }}</td>
                    <td class="py-3 px-4">{!! Str::limit($faq->question,70) !!}</td>
                    <td class="py-1 px-4">{{ $faq->category }}</td>
                    <td class="py-1 px-3">
                        <button type="button" class="faq-toggle inline-flex items-center px-2 py-1 rounded {{ $faq->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}" data-id="{{ $faq->id }}"> 
                            <i class="fas {{ $faq->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i> 
                            <span>{{ $faq->is_active ? 'Active' : 'Inactive' }}</span>
                        </button>
                    </td>
                    <td class="py-1 px-2">
                        <a href="{{ route('admin.faqs.edit',$faq) }}" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                     <td class="py-1 px-2">
                         <button type="button" 
                                 class="js-delete-faq text-red-600 hover:underline" 
                                 data-faq-id="{{ $faq->id }}" 
                                 data-faq-question="{{ $faq->question }}">
                             <i class="fas fa-trash"></i> Delete
                         </button>
                     </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <button type="button" id="saveOrderBtn" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Order</button>
    </form>
     @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Delete FAQ</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to delete the FAQ "<span id="faqQuestion" class="font-semibold"></span>"? This will permanently remove it from the system.</p>
        <form id="deleteForm" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-200">
                    Delete FAQ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function(){
    // Drag-and-drop functionality
    $('#faqTableBody').sortable({
        handle: '.sort-handle',
        placeholder: 'bg-yellow-100',
        axis: 'y',
        cursor: 'move'
    });
    
    // Save order button
    $('#saveOrderBtn').on('click', function(){
        var ids = $('#faqTableBody').sortable('toArray', {attribute:'data-id'});
        $.ajax({
            url: '/admin/faqs/reorder',
            method: 'POST',
            data: {
                ids: ids, 
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toast !== 'undefined') {
                        toast.success('Order saved successfully!');
                    } else {
                        alert('Order saved successfully!');
                    }
                    // Reload to show updated order numbers
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function() {
                if (typeof toast !== 'undefined') {
                    toast.error('Failed to save order');
                } else {
                    alert('Failed to save order');
                }
            }
        });
    });
    
    // Toggle active status
    $('.faq-toggle').on('click', function(){
        var button = $(this);
        var id = button.data('id');
        
        $.ajax({
            url: '/admin/faqs/' + id + '/toggle',
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update button appearance
                    if (response.is_active) {
                        button.removeClass('bg-gray-100 text-gray-600').addClass('bg-green-100 text-green-700');
                        button.find('i').removeClass('fa-toggle-off').addClass('fa-toggle-on');
                        button.find('span').text('Active');
                    } else {
                        button.removeClass('bg-green-100 text-green-700').addClass('bg-gray-100 text-gray-600');
                        button.find('i').removeClass('fa-toggle-on').addClass('fa-toggle-off');
                        button.find('span').text('Inactive');
                    }
                }
            },
            error: function() {
                if (typeof toast !== 'undefined') {
                    toast.error('Failed to update status');
                } else {
                    alert('Failed to update status');
                }
            }
        });
     });
});

// Delete FAQ modal functions
function deleteFaq(id, question) {
    document.getElementById('faqQuestion').textContent = question;
    document.getElementById('deleteForm').action = `/admin/faqs/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

// Delegated handler for delete buttons
document.addEventListener('click', function (event) {
    const deleteBtn = event.target.closest('.js-delete-faq');
    if (deleteBtn) {
        const id = deleteBtn.getAttribute('data-faq-id');
        const question = deleteBtn.getAttribute('data-faq-question');
        if (id) deleteFaq(id, question);
    }
});
</script>
@endpush
