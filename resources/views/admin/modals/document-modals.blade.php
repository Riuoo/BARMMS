<!-- Document Details Modal -->
<div id="documentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-file-signature mr-2 text-blue-600"></i>
                    Document Request Details
                </h3>
                <button onclick="closeDocumentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="documentDetailsContent" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeDocumentDetailsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Document modal functions
function viewDocumentDetails(id) {
    // Show loading state
    document.getElementById('documentDetailsContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading details...</span>
        </div>
    `;
    
    document.getElementById('documentDetailsModal').classList.remove('hidden');
    
    // Fetch document details via AJAX
    fetch(`/admin/document-requests/${id}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('documentDetailsContent').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Requester</h4>
                        <p class="text-gray-600">${data.user_name || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Document Type</h4>
                        <p class="text-gray-600">${data.document_type || 'N/A'}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Purpose</h4>
                    <p class="text-gray-600">${data.purpose || 'No purpose provided'}</p>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Status</h4>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        ${data.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                          data.status === 'approved' ? 'bg-green-100 text-green-800' : 
                          'bg-purple-100 text-purple-800'}">
                        <i class="fas fa-tag mr-1"></i>
                        ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                    </span>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Date Requested</h4>
                    <p class="text-gray-600">${data.created_at}</p>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('documentDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Error loading details. Please try again.</p>
                </div>
            `;
        });
}

function closeDocumentDetailsModal() {
    document.getElementById('documentDetailsModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['documentDetailsModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['documentDetailsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});
</script> 