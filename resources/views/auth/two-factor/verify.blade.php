@extends('layouts.public')

@section('title', 'Two-Factor Authentication Verification')

@section('content')
<div class="min-h-screen hero-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="glass-effect rounded-lg shadow-xl p-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Two-Factor Authentication</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter the 6-digit code from your authenticator app
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('2fa.verify.post') }}">
                @csrf

                <div>
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
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input 
                        id="remember_device" 
                        name="remember_device" 
                        type="checkbox" 
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                    >
                    <label for="remember_device" class="ml-2 block text-sm text-gray-700">
                        Remember this device for 30 days
                    </label>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200"
                    >
                        <i class="fas fa-check mr-2"></i>
                        Verify & Continue
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Login
                    </a>
                </div>
            </form>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Having trouble?</strong> Make sure your device's time is synchronized correctly.
                </p>
            </div>
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

