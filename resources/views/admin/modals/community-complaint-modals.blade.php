<!-- Complaint Details Modal -->
<div id="complaintDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complaint Details</h3>
                <button onclick="document.getElementById('complaintDetailsModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="complaintDetailsContent" class="max-h-96 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('complaintDetailsModal').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Complaint Status</h3>
                <button onclick="closeUpdateModal()" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="updateStatusForm" method="POST" action="">
                @csrf
                @method('POST')
                <input type="hidden" id="updateComplaintId" name="complaint_id">
                
                <div class="space-y-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select status</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeUpdateModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="updateSubmitBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global function to open update status modal
function openUpdateStatusModal(id) {
    console.log('Opening update modal for complaint ID:', id);
    
    // Set the complaint ID
    document.getElementById('updateComplaintId').value = id;
    
    // Set the form action
    const form = document.getElementById('updateStatusForm');
    form.action = `/admin/community-complaints/${id}/update-status`;
    
    // Show the modal
    document.getElementById('updateStatusModal').classList.remove('hidden');
    
    // Add form submission handler
    form.onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = document.getElementById('updateSubmitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Network response was not ok');
        })
        .then(data => {
            // Close modal
            closeUpdateModal();
            
            // Show success message
            if (typeof notify !== 'undefined') {
                notify('success', 'Complaint status updated successfully');
            } else {
                alert('Complaint status updated successfully');
            }
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            console.error('Error updating complaint:', error);
            
            // Show error message
            if (typeof notify !== 'undefined') {
                notify('error', 'Failed to update complaint status');
            } else {
                alert('Failed to update complaint status');
            }
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    };
}

// Function to close update modal
function closeUpdateModal() {
    document.getElementById('updateStatusModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const complaintDetailsModal = document.getElementById('complaintDetailsModal');
    const updateStatusModal = document.getElementById('updateStatusModal');
    
    if (event.target === complaintDetailsModal) {
        complaintDetailsModal.classList.add('hidden');
    }
    
    if (event.target === updateStatusModal) {
        closeUpdateModal();
    }
});

// Close modals with escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('complaintDetailsModal').classList.add('hidden');
        closeUpdateModal();
    }
});

// Debug: Log when script loads
console.log('Community complaint modals script loaded');
console.log('openUpdateStatusModal function available:', typeof openUpdateStatusModal);

document.addEventListener('DOMContentLoaded', function() {
    // Add AJAX form submit for update status
    const updateStatusForm = document.getElementById('updateStatusForm');
    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const action = form.action;
            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    return response.text().then(text => { throw new Error(text); });
                }
            })
            .catch(error => {
                alert('Failed to update status. Please try again.');
            });
        });
    }
});
</script> 