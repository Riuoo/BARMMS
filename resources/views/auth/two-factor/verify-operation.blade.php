@extends('admin.main.layout')

@section('title', 'Two-Factor Authentication Required')

@section('content')
<div class="max-w-md mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-yellow-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Additional Verification Required</h2>
            <p class="text-gray-600 mt-2">
                This action requires two-factor authentication for security
            </p>
        </div>

        <form method="POST" action="{{ route('2fa.verify-operation.post') }}">
            @csrf
            <input type="hidden" name="operation" value="{{ $operation }}">
            <input type="hidden" name="redirect" value="{{ $redirectTo }}">

            <div class="mb-6">
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
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-3">
                <button 
                    type="submit" 
                    class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                >
                    <i class="fas fa-check mr-2"></i>
                    Verify & Continue
                </button>
                <a 
                    href="{{ $redirectTo }}" 
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200"
                >
                    Cancel
                </a>
            </div>
        </form>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                This verification is valid for 15 minutes for this operation.
            </p>
        </div>
    </div>
</div>

<script>
    // Auto-format code input
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection

