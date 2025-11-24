@extends('resident.layout')

@section('title', 'My QR Code')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My QR Code</h1>
                <p class="text-gray-600">Your unique QR code for attendance and check-in</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
                <a href="{{ route('resident.qr-code.download') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </a>
            </div>
        </div>
    </div>

    <!-- QR Code Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="text-center">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $resident->name }}</h2>
                <p class="text-sm text-gray-500">{{ $resident->email }}</p>
            </div>

            <!-- QR Code Container -->
            <div class="flex justify-center mb-6">
                <div class="bg-white p-4 rounded-lg border-2 border-gray-200 inline-block">
                    <div id="qrcode" class="w-64 h-64"></div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-medium text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    How to Use Your QR Code
                </h3>
                <ul class="text-sm text-blue-800 text-left space-y-1">
                    <li>• Present this QR code at barangay events, seminars, and programs</li>
                    <li>• Use it for health center check-ins and consultations</li>
                    <li>• Show it when claiming medicines or relief goods</li>
                    <li>• Staff will scan it to record your attendance automatically</li>
                </ul>
            </div>

            <!-- QR Code Token for Manual Input -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">
                    <i class="fas fa-key mr-2 text-gray-600"></i>
                    Your QR Code Token (for Manual Input)
                </h3>
                <div class="flex items-center gap-2">
                    <input type="text" 
                           id="qrToken" 
                           value="{{ $qrCodeToken }}" 
                           readonly
                           class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-mono text-gray-700">
                    <button onclick="copyToken()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        <i class="fas fa-copy mr-2"></i>Copy
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Use this token for manual entry if the camera scanner is not available.
                </p>
            </div>

            <!-- Security Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <strong>Security:</strong> This QR code contains only a secure token. No personal information is stored in the code itself.
                </p>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">
                <i class="fas fa-mobile-alt mr-2 text-green-600"></i>
                Mobile Friendly
            </h3>
            <p class="text-sm text-gray-600">You can display this QR code on your mobile device screen for scanning.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">
                <i class="fas fa-print mr-2 text-blue-600"></i>
                Printable
            </h3>
            <p class="text-sm text-gray-600">Download and print your QR code for easy access at events.</p>
        </div>
    </div>
</div>

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR Code
    const qrCodeData = @json($qrCodeData);
    
    new QRCode(document.getElementById("qrcode"), {
        text: qrCodeData,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
});

function copyToken() {
    const tokenInput = document.getElementById('qrToken');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 2000);
    } catch (err) {
        // Fallback for browsers that don't support execCommand
        navigator.clipboard.writeText(tokenInput.value).then(() => {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }).catch(() => {
            alert('Failed to copy. Please manually select and copy the token.');
        });
    }
}
</script>
@endpush
@endsection

