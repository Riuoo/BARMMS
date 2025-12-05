@extends(isset($layout) ? $layout : 'admin.main.layout')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Setup Two-Factor Authentication</h2>
            <p class="text-gray-600 mt-2">Secure your account with an extra layer of protection</p>
        </div>

        <div class="space-y-6">
            <!-- Step 1: Scan QR Code -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Scan QR Code</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.)
                </p>
                
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-200">
                        {!! $qrCodeSvg !!}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Can't scan? Enter this code manually:</p>
                    <div class="flex items-center justify-between bg-white p-3 rounded border border-gray-300">
                        <code class="text-lg font-mono text-gray-900">{{ $secret }}</code>
                        <button onclick="copySecret()" class="ml-2 text-green-600 hover:text-green-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Verify Code -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Verify Code</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Enter the 6-digit code from your authenticator app to complete setup
                </p>

                <form method="POST" action="{{ route('2fa.enable') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Verification Code
                        </label>
                        <input 
                            type="text" 
                            id="code" 
                            name="code" 
                            maxlength="6" 
                            pattern="[0-9]{6}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl font-mono tracking-widest"
                            placeholder="000000"
                            autocomplete="off"
                            required
                            autofocus
                        >
                        <p class="text-xs text-gray-500 mt-2">Enter the 6-digit code from your authenticator app</p>
                    </div>

                    <div class="flex space-x-3">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                        >
                            <i class="fas fa-check mr-2"></i>
                            Enable 2FA
                        </button>
                        <a 
                            href="{{ isset($cancelRoute) ? $cancelRoute : route('admin.dashboard') }}" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Need Help?
                </h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Download an authenticator app from your app store</li>
                    <li>• Popular apps: Google Authenticator, Authy, Microsoft Authenticator</li>
                    <li>• The code refreshes every 30 seconds</li>
                    <li>• Keep your backup codes in a safe place</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function copySecret() {
        const secret = '{{ $secret }}';
        navigator.clipboard.writeText(secret).then(() => {
            alert('Secret code copied to clipboard!');
        }).catch(() => {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = secret;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Secret code copied to clipboard!');
        });
    }

    // Auto-format code input
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection

