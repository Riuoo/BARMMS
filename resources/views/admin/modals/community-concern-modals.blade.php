<!-- Complaint Details Modal (styled similar to document request description modal) -->
<div id="complaintDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative mx-auto my-12 w-11/12 md:w-4/5 lg:w-3/5 xl:w-2/5">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100">
            <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100">
                <div class="flex items-start space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-clipboard-list text-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-wide text-gray-500">Community Concern</p>
                        <h3 class="text-xl font-semibold text-gray-900">Concern Details</h3>
                    </div>
                </div>
                <button onclick="document.getElementById('complaintDetailsModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div id="complaintDetailsContent" class="max-h-96 overflow-y-auto pr-1 custom-scrollbar">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-2">
                    <p class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Review the concern carefully before updating its status.
                    </p>
                    <div class="flex items-center justify-end space-x-2 w-full md:w-auto">
                        <button onclick="document.getElementById('complaintDetailsModal').classList.add('hidden')" 
                                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Concern Status</h3>
                <button onclick="closeUpdateModal()" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="updateStatusForm" method="POST" action="">
                @csrf
                @method('POST')
                <input type="hidden" id="updateComplaintId" name="complaint_id">
                <input type="hidden" id="currentComplaintStatus" name="current_status">
                
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

                    <div id="remarksWrapper" class="hidden">
                        <label for="admin_remarks" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason / Remarks <span class="text-red-500">*</span>
                        </label>
                        <textarea id="admin_remarks" name="admin_remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Briefly explain why this concern is resolved or closed..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Example for Closed: “Visited location, no issue requiring action was found.”<br>
                            Example for Resolved: “Coordinated cleanup and cleared the reported obstruction.”
                        </p>
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
function openUpdateStatusModal(id, currentStatus) { // Added currentStatus parameter
    console.log('Opening update modal for concern ID:', id, 'with current status:', currentStatus);
    
    // Set the complaint ID
    document.getElementById('updateComplaintId').value = id;
    // Set the current status
    document.getElementById('currentComplaintStatus').value = currentStatus;
    
    // Set the form action
    const form = document.getElementById('updateStatusForm');
            form.action = `/admin/community-concerns/${id}/update-status`;
    
    // Get the status dropdown
    const statusSelect = document.getElementById('status');
    const remarksWrapper = document.getElementById('remarksWrapper');
    const remarksField = document.getElementById('admin_remarks');
    
    // Define the status order
    const statusOrder = ['pending', 'under_review', 'in_progress', 'resolved', 'closed'];
    
    // Clear existing options
    statusSelect.innerHTML = '<option value="">Select status</option>';

    // Add options based on current status
    let foundCurrentStatus = false;
    for (let i = 0; i < statusOrder.length; i++) {
        const status = statusOrder[i];
        const option = document.createElement('option');
        option.value = status;
        option.textContent = status.replace('_', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

        if (status === currentStatus) {
            foundCurrentStatus = true;
            option.selected = true; // Select the current status
            option.disabled = true; // Disable the current status
        } else if (foundCurrentStatus) {
            // Only allow statuses that come after the current status in the defined order
            statusSelect.appendChild(option);
        } else {
            // Disable statuses that come before the current status
            option.disabled = true;
        }
    }

    // Helper to toggle remarks visibility based on selected status
    function updateRemarksVisibility() {
        const value = statusSelect.value;
        const needsRemarks = value === 'resolved' || value === 'closed';
        if (needsRemarks) {
            remarksWrapper.classList.remove('hidden');
            remarksField.setAttribute('required', 'required');
        } else {
            remarksWrapper.classList.add('hidden');
            remarksField.removeAttribute('required');
            remarksField.value = '';
        }
    }

    // Attach change handler and initialize
    statusSelect.addEventListener('change', updateRemarksVisibility);
    updateRemarksVisibility();

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
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text); });
            }
            return Promise.resolve();
        })
        .then(() => {
            // Close modal
            closeUpdateModal();
            
            // Persist notify and reload
            try { localStorage.setItem('showComplaintUpdateNotify', '1'); } catch (e) {}
            setTimeout(() => { window.location.reload(); }, 200);
        })
        .catch(error => {
            console.error('Error updating concern:', error);
            
            // Show error message
            if (typeof notify !== 'undefined') {
                notify('error', 'Failed to update concern status');
            } else {
                alert('Failed to update concern status');
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
console.log('Community concern modals script loaded');
console.log('openUpdateStatusModal function available:', typeof openUpdateStatusModal);

// View concern details (desktop and mobile)
function viewComplaintDetails(id) {
    console.log('viewComplaintDetails called with ID:', id);
    const modal = document.getElementById('complaintDetailsModal');
    const content = document.getElementById('complaintDetailsContent');
    if (!modal || !content) {
        console.error('Modal or content element not found');
        return;
    }
    content.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div><span class="ml-2 text-gray-500 text-sm">Loading...</span></div>';
    modal.classList.remove('hidden');

    fetch(`/admin/community-concerns/${id}/details`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
        .then(r => r.json())
        .then(data => {
            const title = (data && data.title) ? data.title : 'Concern';
            const location = (data && data.location) ? data.location : 'Not specified';
            const description = (data && data.description) ? data.description : 'No description provided.';
            const status = (data && data.status) ? data.status : 'pending';
            const createdAt = (data && data.created_at) ? data.created_at : 'N/A';
            const assignedAt = (data && data.assigned_at) ? data.assigned_at : 'Not assigned';
            const resolvedAt = (data && data.resolved_at) ? data.resolved_at : 'Not resolved';
            const closedAt = (data && data.closed_at) ? data.closed_at : 'Not closed';
            const remarks = (data && data.admin_remarks) ? data.admin_remarks : null;
            const remarksTimestamp = (data && data.remarks_timestamp) ? data.remarks_timestamp : null;
            const media = Array.isArray(data && data.media_files) ? data.media_files : [];
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-heading"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Title</div>
                                <div class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(title)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Location</div>
                                <div class="text-sm text-gray-900">${escapeHtml(location)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Description</div>
                                <div class="text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(description)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Status & Timeline</div>
                                <div class="mt-1 text-sm text-gray-700">
                                    <div><span class="font-semibold">Current Status:</span> ${escapeHtml(status.replace('_',' ').replace(/\b\w/g, c=>c.toUpperCase()))}</div>
                                    <div><span class="font-semibold">Filed:</span> ${escapeHtml(createdAt)}</div>
                                    <div><span class="font-semibold">Assigned:</span> ${escapeHtml(assignedAt)}</div>
                                    <div><span class="font-semibold">Resolved:</span> ${escapeHtml(resolvedAt)}</div>
                                    <div><span class="font-semibold">Closed:</span> ${escapeHtml(closedAt)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${remarks ? `
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Admin Remarks</div>
                                <div class="text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(remarks)}</div>
                                ${remarksTimestamp ? `<div class="mt-1 text-xs text-gray-500">${escapeHtml(remarksTimestamp)}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    ${media.length ? `
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Attachments</div>
                                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    ${media.map(f => {
                                        const safeName = escapeHtml(f.name || 'File');
                                        const safeUrl = f.url || '#';
                                        const type = (f.type || '').toLowerCase();
                                        const isImage = type.startsWith('image/');
                                        return isImage
                                            ? `<div class="group border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                                    <button type="button"
                                                            class="w-full h-24 flex items-center justify-center bg-black/5"
                                                            onclick="openConcernImageLightbox('${safeUrl.replace(/'/g, '&#039;')}', '${safeName}')">
                                                        <img src="${safeUrl}" alt="${safeName}" class="max-h-24 w-full object-cover group-hover:opacity-90 transition" loading="lazy">
                                                    </button>
                                                    <div class="px-2 py-1 border-t border-gray-100">
                                                        <p class="text-[11px] text-gray-600 truncate" title="${safeName}">
                                                            <i class="fas fa-image mr-1 text-gray-400"></i>${safeName}
                                                        </p>
                                                    </div>
                                               </div>`
                                            : `<a href="${safeUrl}" target="_blank"
                                                   class="flex items-center px-3 py-2 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition">
                                                   <i class="fas fa-file mr-2 text-gray-500"></i>
                                                   <span class="text-xs text-gray-700 truncate" title="${safeName}">${safeName}</span>
                                               </a>`;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>`;
        })
        .catch(() => {
            content.innerHTML = '<p class="text-sm text-red-600">Failed to load concern details.</p>';
        });
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Simple lightbox for concern images
function openConcernImageLightbox(url, caption) {
    const safeCaption = escapeHtml(caption || '');
    const existing = document.getElementById('concernImageLightbox');
    if (existing) existing.remove();
    const html = `
        <div id="concernImageLightbox" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[9999]">
            <div class="relative max-w-3xl w-11/12 md:w-3/4 lg:w-1/2">
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                <i class="fas fa-image text-sm"></i>
                            </span>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Attached Image</p>
                                <p class="text-sm font-medium text-gray-900 truncate" title="${safeCaption}">${safeCaption || 'Preview'}</p>
                            </div>
                        </div>
                        <button type="button"
                                class="text-gray-400 hover:text-gray-600"
                                onclick="closeConcernImageLightbox()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="bg-black flex items-center justify-center">
                        <img src="${url}" alt="${safeCaption}" class="max-h-[70vh] w-full object-contain bg-black">
                    </div>
                </div>
            </div>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', html);
    document.addEventListener('keydown', concernLightboxEscHandler);
}

function closeConcernImageLightbox() {
    const lb = document.getElementById('concernImageLightbox');
    if (lb) lb.remove();
    document.removeEventListener('keydown', concernLightboxEscHandler);
}

function concernLightboxEscHandler(e) {
    if (e.key === 'Escape') {
        closeConcernImageLightbox();
    }
}

// Mobile-specific modal for full complaint details
function viewComplaintDetailsMobile(id) {
    // Build or select mobile modal
    let modal = document.getElementById('complaintDetailsModalMobile');
    if (!modal) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
        <div id="complaintDetailsModalMobile" class="fixed inset-0 bg-gray-700 bg-opacity-60 overflow-y-auto h-full w-full z-50 md:hidden hidden">
            <div class="relative top-10 mx-auto p-4 w-11/12">
                <div class="bg-white rounded-xl shadow-xl border border-gray-200">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-base font-semibold text-gray-900">
                            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                            Concern Details
                        </h3>
                        <button onclick="document.getElementById('complaintDetailsModalMobile').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div id="complaintDetailsMobileContent" class="p-4 space-y-3"></div>
                    <div class="flex justify-end p-4 border-t">
                        <button onclick="document.getElementById('complaintDetailsModalMobile').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Close</button>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.appendChild(wrapper.firstElementChild);
        modal = document.getElementById('complaintDetailsModalMobile');
    }
    const content = document.getElementById('complaintDetailsMobileContent');
    content.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-500 text-sm">Loading...</span></div>';
    modal.classList.remove('hidden');

    fetch(`/admin/community-concerns/${id}/details`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const title = data?.title || 'Concern';
            const location = data?.location || 'Not specified';
            const description = data?.description || 'No description provided.';
            const userName = data?.user_name || 'N/A';
            const status = data?.status || 'pending';
            const createdAt = data?.created_at || 'N/A';
            const assignedAt = data?.assigned_at || 'Not assigned';
            const resolvedAt = data?.resolved_at || 'Not resolved';
            const closedAt = data?.closed_at || 'Not closed';
            const adminRemarks = data?.admin_remarks || null;
            const remarksTimestamp = data?.remarks_timestamp || null;
            const media = Array.isArray(data?.media_files) ? data.media_files : [];

            const statusClass = (s => {
                switch(s){
                    case 'pending': return 'bg-yellow-100 text-yellow-800';
                    case 'under_review': return 'bg-blue-100 text-blue-800';
                    case 'in_progress': return 'bg-orange-100 text-orange-800';
                    case 'resolved': return 'bg-green-100 text-green-800';
                    case 'closed': return 'bg-purple-100 text-purple-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            })(status);

            content.innerHTML = `
                <div class="space-y-3">
                    <div class="p-3 rounded-lg border border-gray-200">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-heading mr-1 text-blue-600"></i> Title</div>
                        <div class="text-sm font-semibold text-gray-900">${escapeHtml(title)}</div>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-user mr-1 text-indigo-600"></i> Submitted By</div>
                            <div class="text-sm text-gray-900">${escapeHtml(userName)}</div>
                        </div>
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-info-circle mr-1 text-teal-600"></i> Status</div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                                <i class="fas fa-circle mr-1 text-[8px]"></i>
                                ${escapeHtml(status.replace('_',' ').replace(/\b\w/g, c=>c.toUpperCase()))}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-calendar mr-1 text-gray-600"></i> Date Filed</div>
                            <div class="text-sm text-gray-900">${escapeHtml(createdAt)}</div>
                        </div>
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-user-check mr-1 text-blue-600"></i> Assigned At</div>
                            <div class="text-sm text-gray-900">${escapeHtml(assignedAt)}</div>
                        </div>
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-check-circle mr-1 text-green-600"></i> Resolved At</div>
                            <div class="text-sm text-gray-900">${escapeHtml(resolvedAt)}</div>
                        </div>
                        <div class="p-3 rounded-lg border border-gray-200">
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-door-closed mr-1 text-purple-600"></i> Closed At</div>
                            <div class="text-sm text-gray-900">${escapeHtml(closedAt)}</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg border border-gray-200">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-map-marker-alt mr-1 text-green-600"></i> Location</div>
                        <div class="text-sm text-gray-900">${escapeHtml(location)}</div>
                    </div>
                    <div class="p-3 rounded-lg border border-gray-200">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-align-left mr-1 text-yellow-600"></i> Description</div>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(description)}</div>
                    </div>
                    ${adminRemarks ? `
                    <div class="p-3 rounded-lg border border-gray-200">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1"><i class="fas fa-comment-dots mr-1 text-purple-600"></i> Admin Remarks</div>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(adminRemarks)}</div>
                        ${remarksTimestamp ? `<div class="mt-1 text-xs text-gray-500">Updated on ${escapeHtml(remarksTimestamp)}</div>` : ''}
                    </div>` : ''}
                    ${media.length ? `
                    <div class="p-3 rounded-lg border border-gray-200">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-2"><i class="fas fa-paperclip mr-1 text-gray-600"></i> Attachments</div>
                        <div class="space-y-2">
                            ${media.map(f => `
                                <a href="${f.url}" target="_blank" class="flex items-center p-2 border rounded hover:bg-gray-50">
                                    <i class="fas fa-file mr-2 text-gray-500"></i>
                                    <span class="text-sm text-gray-700 truncate">${escapeHtml(f.name || 'File')}</span>
                                </a>
                            `).join('')}
                        </div>
                    </div>` : ''}
                </div>`;
        })
        .catch(() => {
            content.innerHTML = '<p class="text-sm text-red-600">Failed to load concern details.</p>';
        });
}

// Close mobile modal on Escape and outside click
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        const m = document.getElementById('complaintDetailsModalMobile');
        if (m && !m.classList.contains('hidden')) m.classList.add('hidden');
    }
});

// Delegate clicks to avoid inline handlers
document.addEventListener('click', function(e){
    const desktopBtn = e.target.closest('.js-concern-view');
    if (desktopBtn) {
        console.log('Desktop concern view button clicked');
        const id = desktopBtn.getAttribute('data-id');
        console.log('Concern ID:', id);
        if (id) viewComplaintDetails(id);
        return;
    }
    const mobileBtn = e.target.closest('.js-concern-view-mobile');
    if (mobileBtn) {
        const id = mobileBtn.getAttribute('data-id');
        if (id) viewComplaintDetailsMobile(id);
        return;
    }
    const updateBtn = e.target.closest('.js-open-update');
    if (updateBtn) {
        const id = updateBtn.getAttribute('data-id');
        const status = updateBtn.getAttribute('data-status');
        if (id) openUpdateStatusModal(id, status || 'pending');
        return;
    }
});
document.addEventListener('click', function(e){
    const m = document.getElementById('complaintDetailsModalMobile');
    if (!m || m.classList.contains('hidden')) return;
    if (e.target === m) m.classList.add('hidden');
});

// Handle status update form submission
document.getElementById('updateStatusForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Set button to processing
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Close modal
            closeUpdateModal();
            
            // Show success toast
            if (typeof notify === 'function') {
                notify('success', 'Concern status updated successfully.');
            } else if (window.toast && typeof window.toast.success === 'function') {
                window.toast.success('Concern status updated successfully.');
            } else {
                alert('Concern status updated successfully.');
            }
            
            // Reload page to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Show error toast
            const errorMsg = data.message || 'Failed to update concern status.';
            if (typeof notify === 'function') {
                notify('error', errorMsg);
            } else if (window.toast && typeof window.toast.error === 'function') {
                window.toast.error(errorMsg);
            } else {
                alert(errorMsg);
            }
        }
    } catch (error) {
        console.error('Error updating status:', error);
        if (typeof notify === 'function') {
            notify('error', 'Network error while updating status.');
        } else if (window.toast && typeof window.toast.error === 'function') {
            window.toast.error('Network error while updating status.');
        } else {
            alert('Network error while updating status.');
        }
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
});
</script>