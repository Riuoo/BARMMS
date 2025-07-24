<!-- Blotter Details Modal -->
<div id="blotterDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-file-alt mr-2 text-red-600"></i>
                    Blotter Report Details
                </h3>
                <button onclick="closeBlotterDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="blotterDetailsContent" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeBlotterDetailsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Blotter Modal -->
<div id="approveBlotterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                    Approve Blotter Report
                </h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="approveBlotterForm" method="POST" onsubmit="return approveAndDownload(event)">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        Hearing Date
                    </label>
                    <input type="datetime-local" name="hearing_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           required>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApproveModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-check mr-1"></i>
                        Approve Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Summon Modal -->
<div id="newSummonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-file-alt mr-2 text-teal-600"></i>
                    Create New Summon
                </h3>
                <button onclick="closeNewSummonModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="newSummonForm" method="POST" onsubmit="return summonAndDownload(event)">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>
                        New Summon Date
                    </label>
                    <input type="date" name="new_summon_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           min="{{ date('Y-m-d') }}"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Select a date for the new summon notice</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeNewSummonModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition duration-200">
                        <i class="fas fa-file-alt mr-1"></i>
                        Generate Summon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Media Viewer Modal -->
<div id="mediaViewerModal" class="fixed inset-0 bg-gray-900 bg-opacity-90 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 w-11/12 md:w-3/4 lg:w-1/2">
        <div class="bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-image mr-2"></i>
                    Media Viewer
                </h3>
                <button onclick="closeMediaViewer()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="mediaViewerContent" class="p-4">
                <!-- Media content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Modal functions
function viewBlotterDetails(id) {
    // Show loading state
    document.getElementById('blotterDetailsContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
            <span class="ml-2 text-gray-600">Loading details...</span>
        </div>
    `;
    
    document.getElementById('blotterDetailsModal').classList.remove('hidden');
    
    // Fetch blotter details via AJAX
    fetch(`/admin/blotter-reports/${id}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('blotterDetailsContent').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Complainant</h4>
                        <p class="text-gray-600">${data.user_name || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Respondent</h4>
                        <p class="text-gray-600">${data.recipient_name || 'N/A'}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                    <p class="text-gray-600">${data.description || 'No description provided'}</p>
                </div>
                
                ${data.media_files ? `
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Attached Files</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        ${data.media_files.map(file => `
                            <button onclick="viewMedia('${file.url}')" 
                                    class="p-2 border border-gray-200 rounded-md hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-file mr-1"></i>
                                ${file.name}
                            </button>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Status</h4>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        ${data.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                          data.status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                          'bg-green-100 text-green-800'}">
                        <i class="fas fa-tag mr-1"></i>
                        ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                    </span>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Date Filed</h4>
                    <p class="text-gray-600">${data.created_at}</p>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('blotterDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-gray-600">Error loading details. Please try again.</p>
                </div>
            `;
        });
}

function closeBlotterDetailsModal() {
    document.getElementById('blotterDetailsModal').classList.add('hidden');
}

function openApproveModal(id) {
    document.getElementById('approveBlotterForm').action = `/admin/blotter-reports/${id}/approve`;
    document.getElementById('approveBlotterModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveBlotterModal').classList.add('hidden');
    document.getElementById('approveBlotterForm').reset();
}

function openNewSummonModal(id) {
    // Set the form action and show modal
    document.getElementById('newSummonForm').action = `/admin/blotter-reports/${id}/new-summons`;
    document.getElementById('newSummonModal').classList.remove('hidden');
}

function closeNewSummonModal() {
    document.getElementById('newSummonModal').classList.add('hidden');
    document.getElementById('newSummonForm').reset();
}

function viewMedia(url) {
    document.getElementById('mediaViewerContent').innerHTML = `
        <div class="text-center">
            <img src="${url}" alt="Media file" class="max-w-full h-auto mx-auto rounded-lg">
        </div>
    `;
    document.getElementById('mediaViewerModal').classList.remove('hidden');
}

function closeMediaViewer() {
    document.getElementById('mediaViewerModal').classList.add('hidden');
}

function refreshToBlotterReports() {
    location.reload();
}

function approveAndDownload(event) {
    event.preventDefault();
    const form = event.target;
    const action = form.action;
    const blotterIdMatch = action.match(/blotter-reports\/(\d+)\/approve/);
    const blotterId = blotterIdMatch ? blotterIdMatch[1] : '';
    const formData = new FormData(form);
    fetch(action, {
        method: 'POST',
        headers: {
            'Accept': 'application/pdf',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `blotter_approve_${blotterId}.pdf`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            setTimeout(() => location.reload(), 1000);
        } else {
            // Try to extract error message from response
            let errorMsg = 'Error approving and downloading PDF.';
            try {
                const text = await response.text();
                if (text.includes('This user account is inactive')) {
                    errorMsg = 'This user account is inactive and cannot make transactions.';
                } else if (text.includes('<ul class="list-disc')) {
                    const match = text.match(/<li>(.*?)<\/li>/);
                    if (match) errorMsg = match[1];
                }
            } catch (e) {}
            alert(errorMsg);
        }
    })
    .catch(error => {
        alert('Error approving and downloading PDF.');
        console.error(error);
    });
    return false;
}

function summonAndDownload(event) {
    event.preventDefault();
    const form = event.target;
    const action = form.action;
    const blotterIdMatch = action.match(/blotter-reports\/(\d+)\/new-summons/);
    const blotterId = blotterIdMatch ? blotterIdMatch[1] : '';
    const formData = new FormData(form);
    fetch(action, {
        method: 'POST',
        headers: {
            'Accept': 'application/pdf',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        if (response.ok && contentType.includes('application/pdf')) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `blotter_summon_${blotterId}.pdf`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            setTimeout(() => location.reload(), 1000);
        } else {
            // Try to extract error message from response
            let errorMsg = 'Error generating summon and downloading PDF.';
            try {
                const text = await response.text();
                if (text.includes('This user account is inactive')) {
                    errorMsg = 'This user account is inactive and cannot make transactions.';
                } else if (text.includes('<ul class="list-disc')) {
                    const match = text.match(/<li>(.*?)<\/li>/);
                    if (match) errorMsg = match[1];
                }
            } catch (e) {}
            alert(errorMsg);
        }
    })
    .catch(error => {
        alert('Error generating summon and downloading PDF.');
        console.error(error);
    });
    return false;
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['blotterDetailsModal', 'approveBlotterModal', 'newSummonModal', 'mediaViewerModal'];
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
        const modals = ['blotterDetailsModal', 'approveBlotterModal', 'newSummonModal', 'mediaViewerModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});
</script> 