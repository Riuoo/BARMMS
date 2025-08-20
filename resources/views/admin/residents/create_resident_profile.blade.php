@extends('admin.main.layout')

@section('title', 'Add New Resident')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 mt-8">
        <h1 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Add New Resident</h1>

        @if(session('success'))
            <div class="mb-6">
                <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6">
                <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.residents.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                <p id="email-warning" class="mt-2 text-sm text-red-600 hidden"></p>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <!-- Demographic Information -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-2">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Demographic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
                        <input type="number" id="age" name="age" value="{{ old('age') }}" min="1" max="120" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                    <div>
                        <label for="family_size" class="block text-sm font-medium text-gray-700 mb-1">Family Size <span class="text-red-500">*</span></label>
                        <input type="number" id="family_size" name="family_size" value="{{ old('family_size') }}" min="1" max="20" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    </div>
                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level <span class="text-red-500">*</span></label>
                        <select id="education_level" name="education_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Education Level</option>
                            <option value="No Education" {{ old('education_level') == 'No Education' ? 'selected' : '' }}>No Education</option>
                            <option value="Elementary" {{ old('education_level') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Vocational" {{ old('education_level') == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="College" {{ old('education_level') == 'College' ? 'selected' : '' }}>College</option>
                            <option value="Post Graduate" {{ old('education_level') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                        </select>
                    </div>
                    <div>
                        <label for="income_level" class="block text-sm font-medium text-gray-700 mb-1">Income Level <span class="text-red-500">*</span></label>
                        <select id="income_level" name="income_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Income Level</option>
                            <option value="Low" {{ old('income_level') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Lower Middle" {{ old('income_level') == 'Lower Middle' ? 'selected' : '' }}>Lower Middle</option>
                            <option value="Middle" {{ old('income_level') == 'Middle' ? 'selected' : '' }}>Middle</option>
                            <option value="Upper Middle" {{ old('income_level') == 'Upper Middle' ? 'selected' : '' }}>Upper Middle</option>
                            <option value="High" {{ old('income_level') == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div>
                        <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status <span class="text-red-500">*</span></label>
                        <select id="employment_status" name="employment_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Employment Status</option>
                            <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Part-time" {{ old('employment_status') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Self-employed" {{ old('employment_status') == 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                            <option value="Full-time" {{ old('employment_status') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        </select>
                    </div>
                    <div>
                        <label for="health_status" class="block text-sm font-medium text-gray-700 mb-1">Health Status <span class="text-red-500">*</span></label>
                        <select id="health_status" name="health_status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                            <option value="">Select Health Status</option>
                            <option value="Critical" {{ old('health_status') == 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="Poor" {{ old('health_status') == 'Poor' ? 'selected' : '' }}>Poor</option>
                            <option value="Fair" {{ old('health_status') == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Good" {{ old('health_status') == 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Excellent" {{ old('health_status') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span>    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between mt-8">
                <a href="{{ route('admin.residents') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" id="submit-btn"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>
                    Add Resident
                </button>
            </div>
        </form>
        <script>
            (function() {
                const emailInput = document.getElementById('email');
                const warning = document.getElementById('email-warning');
                const submitBtn = document.getElementById('submit-btn');
                let lastQueried = '';
                let debounceTimer;

                function checkEmail(value) {
                    if (!value || value === lastQueried) return;
                    lastQueried = value;
                    const baseUrl = `{{ route('admin.residents.check-email') }}`;
                    const url = baseUrl + '?email=' + encodeURIComponent(value);
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                        .then(r => r.json())
                        .then(data => {
                            if (data.blocked) {
                                warning.textContent = 'Account creation blocked: this email has a ' + data.status + ' account request.';
                                warning.classList.remove('hidden');
                                submitBtn.disabled = true;
                            } else {
                                warning.textContent = '';
                                warning.classList.add('hidden');
                                submitBtn.disabled = false;
                            }
                        })
                        .catch(() => {
                            // On error, do not block, just hide warning
                            warning.textContent = '';
                            warning.classList.add('hidden');
                            submitBtn.disabled = false;
                        });
                }

                emailInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const val = this.value.trim();
                    debounceTimer = setTimeout(() => checkEmail(val), 300);
                });

                // Initial check if old('email') exists
                if (emailInput.value) {
                    checkEmail(emailInput.value.trim());
                }
            })();
        </script>
    </div>
@endsection