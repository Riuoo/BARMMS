<!-- Demographics Modal - Nicer & Simpler -->
<div id="demographicsModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto transform transition-all duration-300 ease-out scale-95 opacity-0"
         id="demographicsModalContent">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user-friends text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800" id="modalResidentName"></h3>
                    <p class="text-sm text-gray-500 mt-1">Demographic Information</p>
                </div>
            </div>
            <button onclick="closeDemographicsModal()" 
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 rounded-full p-1 transition duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Modal Body - Demographics Content -->
        <div id="demographicsContent" class="p-5 space-y-4 text-gray-700">
            <!-- Content will be loaded dynamically by JavaScript -->
            <p class="text-center text-gray-500">Loading demographic data...</p>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-end p-5 border-t border-gray-200">
            <button onclick="closeDemographicsModal()" 
                    class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    // Existing JavaScript functions (deleteResident, closeDeleteModal) remain the same

    function showDemographicsModal(residentId, residentName) {
        document.getElementById('modalResidentName').textContent = residentName;
        const demographicsContent = document.getElementById('demographicsContent');
        demographicsContent.innerHTML = '<p class="text-center text-gray-500">Loading demographic data...</p>'; // Loading message

        // Apply initial transform for animation
        const modalContent = document.getElementById('demographicsModalContent');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');

        // Fetch demographics data via AJAX
        fetch(`/admin/residents/${residentId}/demographics`)
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}, message: ${text}`); });
                }
                return response.json();
            })
            .then(data => {
                let contentHtml = '';
                const fields = {
                    'age': { label: 'Age', icon: 'fas fa-birthday-cake' },
                    'gender': { label: 'Gender', icon: 'fas fa-venus-mars' },
                    'civil_status': { label: 'Civil Status', icon: 'fas fa-ring' },
                    'family_size': { label: 'Family Size', icon: 'fas fa-users' },
                    'education_level': { label: 'Education', icon: 'fas fa-graduation-cap' },
                    'income_level': { label: 'Income Level', icon: 'fas fa-money-bill-wave' },
                    'employment_status': { label: 'Employment', icon: 'fas fa-briefcase' },
                    'health_status': { label: 'Health Status', icon: 'fas fa-heartbeat' },
                    'voter_status': { label: 'Voter Status', icon: 'fas fa-vote-yea' },
                    'pwd_status': { label: 'PWD Status', icon: 'fas fa-wheelchair' },
                    'senior_citizen_status': { label: 'Senior Citizen', icon: 'fas fa-user-alt' },
                };

                let hasData = false;
                let dataItems = []; // Collect items to check if any data exists

                for (const key in fields) {
                    if (data[key] !== undefined && data[key] !== null && data[key] !== '') {
                        dataItems.push(`<div class="flex items-center text-base"><i class="${fields[key].icon} text-blue-500 mr-3 text-lg"></i><span class="font-medium">${fields[key].label}:</span> <span class="ml-2">${data[key]}</span></div>`);
                        hasData = true;
                    }
                }

                if (hasData) {
                    contentHtml = `<div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-3">${dataItems.join('')}</div>`;
                } else {
                    contentHtml = '<p class="text-center text-gray-500 py-4">No demographic data available for this resident.</p>';
                }
                
                demographicsContent.innerHTML = contentHtml;
            })
            .catch(error => {
                console.error('Error fetching demographics:', error);
                demographicsContent.innerHTML = `<p class="text-center text-red-500 py-4">Failed to load demographics. Please try again. <br>Details: ${error.message || error}</p>`;
            });

        document.getElementById('demographicsModal').classList.remove('hidden');
        document.getElementById('demographicsModal').classList.add('flex');
    }

    function closeDemographicsModal() {
        const modalContent = document.getElementById('demographicsModalContent');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        // Hide the modal after the transition completes
        modalContent.addEventListener('transitionend', function handler() {
            document.getElementById('demographicsModal').classList.add('hidden');
            document.getElementById('demographicsModal').classList.remove('flex');
            modalContent.removeEventListener('transitionend', handler);
        });
    }
</script>
