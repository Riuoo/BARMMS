@extends('admin.main.layout')

@section('title', 'QR Code Scanner')

@section('content')
@include('components.loading.scanner-skeleton')

<div class="max-w-7xl mx-auto pt-2" id="scannerContent" style="display: none;">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">QR Code Scanner</h1>
        <p class="text-gray-600">Scan resident QR codes for attendance</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scanner Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold mb-4">Scan QR Code</h2>
                
                <!-- Event Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Barangay Activity / Health Activity</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="eventType" class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="event">Barangay Activity / Project</option>
                            <option value="health_center_activity">Health Activity</option>
                        </select>
                        <select id="eventId" class="block w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Select...</option>
                        </select>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Showing all barangay activities/projects and health activities
                    </p>
                </div>

                <!-- Video/Camera -->
                <div class="mb-4">
                    <div id="video-container" class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 400px;">
                        <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center">
                            <div class="border-4 border-green-500 rounded-lg" style="width: 250px; height: 250px;"></div>
                        </div>
                        <div id="camera-status" class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded text-sm">
                            <span id="status-text">Starting camera...</span>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button id="start-camera-btn" onclick="startCamera()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                            <i class="fas fa-camera mr-2"></i>Start Camera
                        </button>
                        <button id="stop-camera-btn" onclick="stopCamera()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm hidden">
                            <i class="fas fa-stop mr-2"></i>Stop Camera
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2 text-center">Position QR code within the frame</p>
                </div>

                <!-- Manual Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Or Enter QR Code Token Manually</label>
                    <div class="flex gap-2">
                        <input type="text" id="manualToken" placeholder="Enter QR code token" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
                        <button onclick="scanManual()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Scan
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Residents can find their token on their "My QR Code" page in their dashboard.
                    </p>
                </div>

                <!-- Guest/Manual Attendance -->
                <div class="mb-4 border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-plus mr-2 text-purple-600"></i>
                        Add Guest/Manual Attendance
                    </h3>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-3">
                        <p class="text-xs text-purple-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Use this for elders or people without accounts who want to attend.
                        </p>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Full Name *</label>
                            <input type="text" id="guestName" placeholder="Enter full name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Contact Number (Optional)</label>
                            <input type="text" id="guestContact" placeholder="Phone number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <button onclick="addGuestAttendance()" 
                                class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">
                            <i class="fas fa-user-plus mr-2"></i>Add Guest Attendance
                        </button>
                    </div>
                </div>

                <!-- Result Message -->
                <div id="scanResult" class="hidden mb-4"></div>
            </div>
        </div>

        <!-- Attendance Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Attendance Info</h2>
                <div class="text-center mb-4">
                    <div class="text-4xl font-bold text-green-600" id="attendanceCount">0</div>
                    <p class="text-sm text-gray-500">Total Attendees</p>
                </div>
                <div id="recentAttendees" class="space-y-2 max-h-64 overflow-y-auto">
                    <p class="text-sm text-gray-500 text-center">No attendees yet</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.attendance.logs') }}" class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-md hover:bg-gray-700">
                        View All Logs
                    </a>
                    <button onclick="refreshAttendance()" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Refresh Count
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let video, canvas, context;
let scanning = false;
let attendanceInterval;
let stream = null;
let scanningInterval = null;
let lastScannedCode = null;
let lastScanTime = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Hide skeleton and show content after a minimum display time
    const skeleton = document.querySelector('[data-skeleton]');
    const content = document.getElementById('scannerContent');
    
    // Minimum skeleton display time (1000ms) for better UX
    const minDisplayTime = 1000;
    const startTime = Date.now();
    
    function showContent() {
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, minDisplayTime - elapsed);
        
        setTimeout(function() {
            if (skeleton) {
                skeleton.style.display = 'none';
            }
            if (content) {
                content.style.display = 'block';
            }
        }, remaining);
    }
    
    // Wait for page to be fully ready
    if (document.readyState === 'complete') {
        showContent();
    } else {
        window.addEventListener('load', showContent);
        // Fallback: show content after max 1 second even if load event doesn't fire
        setTimeout(showContent, 1000);
    }

    video = document.getElementById('video');
    canvas = document.getElementById('canvas');
    context = canvas.getContext('2d');

    // Load events based on type
    document.getElementById('eventType').addEventListener('change', loadEvents);
    loadEvents();

    // Check if camera is supported
    const isLocalhost = window.location.hostname === 'localhost' || 
                       window.location.hostname === '127.0.0.1' ||
                       window.location.hostname === '[::1]';
    const isHTTPS = window.location.protocol === 'https:';
    
    if (!isHTTPS && !isLocalhost) {
        updateCameraStatus('Camera requires HTTPS. Please use manual input or access via HTTPS.', 'error');
    } else {
        // Auto-start camera
        startCamera();
    }

    // Refresh attendance every 5 seconds
    attendanceInterval = setInterval(refreshAttendance, 5000);
    refreshAttendance();
});

function loadEvents() {
    const eventType = document.getElementById('eventType').value;
    const eventSelect = document.getElementById('eventId');
    eventSelect.innerHTML = '<option value="">Select...</option>';

    let firstEventId = null;
    let firstOngoingId = null;

    // Data prepared server-side for clean JS (no Blade helpers inside JS)
    const barangayEvents = {!! $formattedEventsJson !!};
    const healthActivities = {!! $formattedHealthActivitiesJson !!};

    if (eventType === 'event') {
        barangayEvents.forEach((evt, index) => {
            if (firstEventId === null) firstEventId = evt.id;
            const option = document.createElement('option');
            option.value = evt.id;
            option.textContent = evt.label;
            eventSelect.appendChild(option);
        });
    } else if (eventType === 'health_center_activity') {
        healthActivities.forEach((act, index) => {
            if (firstEventId === null) firstEventId = act.id;
            if (firstOngoingId === null && act.is_ongoing) {
                firstOngoingId = act.id;
            }
            const option = document.createElement('option');
            option.value = act.id;
            option.textContent = act.label;
            eventSelect.appendChild(option);
        });
    }

    // Auto-select: prioritize URL-selected event, then ongoing event, then first event
    const initialEventId = {{ $eventId ? (int) $eventId : 'null' }};

    if (initialEventId) {
        eventSelect.value = initialEventId;
        refreshAttendance();
    } else if (firstOngoingId) {
        eventSelect.value = firstOngoingId;
        refreshAttendance();
    } else if (firstEventId) {
        eventSelect.value = firstEventId;
        refreshAttendance();
    }
}

function startCamera() {
    updateCameraStatus('Requesting camera access...', 'info');

    // Get the getUserMedia function with fallbacks for older browsers
    const getUserMedia = navigator.mediaDevices?.getUserMedia ||
                        navigator.getUserMedia ||
                        navigator.webkitGetUserMedia ||
                        navigator.mozGetUserMedia ||
                        navigator.msGetUserMedia;

    if (!getUserMedia) {
        updateCameraStatus('Camera not supported. Use manual input.', 'error');
        return;
    }

    // Try rear camera first, then fallback to any camera
    const constraints = {
        video: {
            facingMode: { ideal: 'environment' },
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }
    };

    // Handle both new and old API
    let promise;
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Modern API
        promise = navigator.mediaDevices.getUserMedia(constraints);
    } else {
        // Legacy API (needs different constraints format)
        const legacyConstraints = {
            video: true,
            audio: false
        };
        promise = new Promise((resolve, reject) => {
            getUserMedia.call(navigator, legacyConstraints, resolve, reject);
        });
    }

    promise
        .then(function(mediaStream) {
            stream = mediaStream;
            
            // Handle both srcObject (modern) and src (legacy)
            if (video.srcObject !== undefined) {
                video.srcObject = stream;
            } else if (video.mozSrcObject !== undefined) {
                video.mozSrcObject = stream;
            } else {
                video.src = URL.createObjectURL(stream);
            }
            
            video.setAttribute('playsinline', true);
            video.setAttribute('webkit-playsinline', true);
            
            video.onloadedmetadata = function() {
                video.play()
                    .then(() => {
                        updateCameraStatus('Camera active', 'success');
                        document.getElementById('start-camera-btn').classList.add('hidden');
                        document.getElementById('stop-camera-btn').classList.remove('hidden');
                        // Start scanning loop
                        startScanning();
                    })
                    .catch(err => {
                        console.error('Error playing video:', err);
                        // Try to play anyway
                        video.play().catch(() => {
                            updateCameraStatus('Error starting video. Try clicking play.', 'error');
                        });
                    });
            };
            
            // Fallback if onloadedmetadata doesn't fire
            setTimeout(() => {
                if (video.readyState >= 2) {
                    video.play()
                        .then(() => {
                            updateCameraStatus('Camera active', 'success');
                            document.getElementById('start-camera-btn').classList.add('hidden');
                            document.getElementById('stop-camera-btn').classList.remove('hidden');
                            startScanning();
                        })
                        .catch(() => {});
                }
            }, 1000);
        })
        .catch(function(err) {
            console.error('Error accessing camera:', err);
            let errorMsg = 'Camera access denied. ';
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                errorMsg += 'Please allow camera access in your browser settings and refresh the page.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                errorMsg += 'No camera found. Please use manual input.';
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                errorMsg += 'Camera is being used by another application.';
            } else if (err.name === 'OverconstrainedError' || err.name === 'ConstraintNotSatisfiedError') {
                // Try with simpler constraints
                errorMsg = 'Trying simpler camera settings...';
                updateCameraStatus(errorMsg, 'info');
                trySimplerCamera();
                return;
            } else {
                errorMsg += 'Please use manual input. Error: ' + (err.message || err.name);
            }
            updateCameraStatus(errorMsg, 'error');
        });
}

function trySimplerCamera() {
    const getUserMedia = navigator.mediaDevices?.getUserMedia ||
                        navigator.getUserMedia ||
                        navigator.webkitGetUserMedia ||
                        navigator.mozGetUserMedia ||
                        navigator.msGetUserMedia;

    const simpleConstraints = { video: true, audio: false };
    
    let promise;
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        promise = navigator.mediaDevices.getUserMedia(simpleConstraints);
    } else {
        promise = new Promise((resolve, reject) => {
            getUserMedia.call(navigator, simpleConstraints, resolve, reject);
        });
    }

    promise
        .then(function(mediaStream) {
            stream = mediaStream;
            if (video.srcObject !== undefined) {
                video.srcObject = stream;
            } else if (video.mozSrcObject !== undefined) {
                video.mozSrcObject = stream;
            } else {
                video.src = URL.createObjectURL(stream);
            }
            video.setAttribute('playsinline', true);
            video.play()
                .then(() => {
                    updateCameraStatus('Camera active', 'success');
                    document.getElementById('start-camera-btn').classList.add('hidden');
                    document.getElementById('stop-camera-btn').classList.remove('hidden');
                    startScanning();
                });
        })
        .catch(function(err) {
            updateCameraStatus('Camera access failed. Please use manual input.', 'error');
        });
}

function stopCamera() {
    if (stream) {
        if (stream.getTracks) {
            stream.getTracks().forEach(track => track.stop());
        } else if (stream.stop) {
            stream.stop();
        }
        stream = null;
    }
    if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
    }
    if (video.srcObject) {
        video.srcObject = null;
    } else if (video.mozSrcObject) {
        video.mozSrcObject = null;
    } else if (video.src) {
        URL.revokeObjectURL(video.src);
        video.src = '';
    }
    document.getElementById('start-camera-btn').classList.remove('hidden');
    document.getElementById('stop-camera-btn').classList.add('hidden');
    updateCameraStatus('Camera stopped', 'info');
}

function updateCameraStatus(message, type) {
    const statusDiv = document.getElementById('camera-status');
    const statusText = document.getElementById('status-text');
    statusText.textContent = message;
    
    if (type === 'error') {
        statusDiv.className = 'absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded text-sm';
    } else if (type === 'success') {
        statusDiv.className = 'absolute top-4 left-4 bg-green-600 text-white px-3 py-1 rounded text-sm';
    } else {
        statusDiv.className = 'absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded text-sm';
    }
}

function startScanning() {
    if (scanningInterval) {
        clearInterval(scanningInterval);
    }
    
    scanningInterval = setInterval(function() {
        if (video.readyState === video.HAVE_ENOUGH_DATA && video.videoWidth > 0 && video.videoHeight > 0) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            try {
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });

                if (code) {
                    processQRCode(code.data);
                }
            } catch (e) {
                console.error('Error scanning QR code:', e);
            }
        }
    }, 100); // Scan every 100ms
}

function processQRCode(data) {
    // Prevent scanning the same code multiple times within 2 seconds
    const now = Date.now();
    if (lastScannedCode === data && (now - lastScanTime) < 2000) {
        return; // Ignore duplicate scan
    }
    
    lastScannedCode = data;
    lastScanTime = now;
    
    // Extract token from URL or use data directly
    let token = data;
    if (data.includes('/qr/verify/')) {
        token = data.split('/qr/verify/')[1];
    } else if (data.includes('qr/verify/')) {
        token = data.split('qr/verify/')[1];
    }

    scanToken(token);
}

function scanToken(token) {
    const eventId = document.getElementById('eventId').value;
    const eventType = document.getElementById('eventType').value;

    if (!eventId) {
        showResult('Please select an event first.', 'error');
        return;
    }

    fetch('{{ route("admin.attendance.scan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            token: token,
            event_id: eventId,
            event_type: eventType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResult(`✓ ${data.resident.name} - Attendance logged!`, 'success');
            refreshAttendance();
        } else {
            showResult(`✗ ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showResult('Error: ' + error.message, 'error');
    });
}

function scanManual() {
    const token = document.getElementById('manualToken').value;
    if (!token) {
        showResult('Please enter a QR code token', 'error');
        return;
    }
    scanToken(token);
    document.getElementById('manualToken').value = '';
}

function showResult(message, type) {
    const resultDiv = document.getElementById('scanResult');
    resultDiv.className = type === 'success' ? 'bg-green-50 border border-green-200 rounded-lg p-4' : 'bg-red-50 border border-red-200 rounded-lg p-4';
    resultDiv.innerHTML = `<p class="text-sm ${type === 'success' ? 'text-green-800' : 'text-red-800'}">${message}</p>`;
    resultDiv.classList.remove('hidden');
    
    setTimeout(() => {
        resultDiv.classList.add('hidden');
    }, 3000);
}

function addGuestAttendance() {
    const guestName = document.getElementById('guestName').value.trim();
    const guestContact = document.getElementById('guestContact').value.trim();
    const eventId = document.getElementById('eventId').value;
    const eventType = document.getElementById('eventType').value;

    if (!guestName) {
        showResult('Please enter a name.', 'error');
        return;
    }

    if (!eventId) {
        showResult('Please select an event first.', 'error');
        return;
    }

    fetch('{{ route("admin.attendance.add-manual") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: guestName,
            contact: guestContact,
            event_id: eventId,
            event_type: eventType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.is_guest === false) {
                // Matched a resident
                showResult(`✓ ${data.resident.name} - Resident attendance logged!`, 'success');
            } else {
                // No match - logged as guest
                showResult(`✓ ${data.guest.name} - Guest attendance logged!`, 'success');
            }
            document.getElementById('guestName').value = '';
            document.getElementById('guestContact').value = '';
            refreshAttendance();
        } else {
            showResult(`✗ ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showResult('Error: ' + error.message, 'error');
    });
}

function refreshAttendance() {
    const eventId = document.getElementById('eventId').value;
    const eventType = document.getElementById('eventType').value;

    if (!eventId) {
        document.getElementById('attendanceCount').textContent = '0';
        document.getElementById('recentAttendees').innerHTML = '<p class="text-sm text-gray-500 text-center">Select an event</p>';
        return;
    }

    fetch(`{{ route("admin.attendance.get") }}?event_id=${eventId}&event_type=${eventType}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('attendanceCount').textContent = data.count;
            
            const attendeesDiv = document.getElementById('recentAttendees');
            if (data.attendees.length === 0) {
                attendeesDiv.innerHTML = '<p class="text-sm text-gray-500 text-center">No attendees yet</p>';
            } else {
                attendeesDiv.innerHTML = data.attendees.slice(0, 5).map(attendee => `
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-sm font-medium">
                            ${attendee.name}
                            ${attendee.is_guest ? '<span class="ml-1 px-1.5 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">Guest</span>' : ''}
                        </p>
                        <p class="text-xs text-gray-500">${attendee.scanned_at}</p>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Error fetching attendance:', error));
}
</script>
@endpush
@endsection

