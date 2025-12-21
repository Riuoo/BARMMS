<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Administrator - Barangay Information System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('lower malinao logo.ico') }}" type="image/x-icon">
    @notifyCss
    <style>
        .notify {
            z-index: 1001 !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('notify::components.notify')
    <!-- Navigation -->
    <nav class="bg-green-600 text-white fixed w-full top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                        <img src="/images/lower-malinao-brgy-logo.png" alt="Lower Malinao Barangay Logo" class="h-10 w-auto" />
                        <h1 class="text-xl font-bold">Lower Malinao System</h1>
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('landing') }}" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Home</a>
                        <a href="#bulletin" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Bulletin Board</a>
                        <a href="#contact" class="hover:bg-green-700 px-3 py-2 rounded-md text-sm font-medium transition duration-300">Contact</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-green-600">
                <a href="{{ route('landing') }}" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="#bulletin" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Bulletin Board</a>
                <a href="#contact" class="block hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20 flex items-center justify-center px-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-800 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h2 class="text-3xl font-bold text-gray-900 text-center flex-1 mr-8">Contact Administrator</h2>
            </div>
            <p class="text-gray-600 mb-2 text-center">
                Please provide your information below to request an account. The administrator will review your request and, if approved, send you a link to complete your account creation.
            </p>
            <p class="text-xs text-gray-500 mb-6 text-center">
                Your name must match an existing resident record in the barangay system, and you must upload a clear photo of a valid ID or official document for verification.
            </p>
           <form action="{{ route('admin.contact.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Full Name Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                    <p class="text-xs text-gray-500">
                        Enter your name exactly as it appears in the barangay resident records. If your details are not yet encoded, please visit the barangay office before submitting this request.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                                placeholder="Enter your first name" value="{{ old('first_name') }}" />
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-0">Middle Name</label>
                                <label class="inline-flex items-center text-xs text-gray-600">
                                    <input type="checkbox" id="no_middle_name" class="mr-1">
                                    I don't have a middle name
                                </label>
                            </div>
                            <input type="text" id="middle_name" name="middle_name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                                placeholder="Enter your middle name (optional)" value="{{ old('middle_name') }}" />
                            @error('middle_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                                placeholder="Enter your last name" value="{{ old('last_name') }}" />
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="suffix" class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                            <select id="suffix" name="suffix" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                                <option value="">None</option>
                                <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                                <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                                <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                            </select>
                            @error('suffix')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Email Section -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                        placeholder="Enter email address (Example: yourname@example.com)" value="{{ old('email') }}" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verification Documents Section -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Verification Documents <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Please upload documents to verify you are a resident of the barangay (e.g., ID, proof of address, barangay clearance, etc.)</p>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-400 transition duration-200">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="verification_documents" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                        <span>Upload files</span>
                                        <input id="verification_documents" name="verification_documents[]" type="file" multiple accept=".pdf,.jpg,.jpeg,.png" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 10MB each</p>
                            </div>
                        </div>
                        <div id="file-list" class="mt-3 space-y-2"></div>
                        @error('verification_documents')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('verification_documents.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Send Request
                </button>
            </form>

        </div>
    </main>

    <!-- JavaScript for mobile menu and file upload -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // File upload preview
        const fileInput = document.getElementById('verification_documents');
        const fileList = document.getElementById('file-list');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                fileList.innerHTML = '';
                const files = Array.from(e.target.files);
                
                if (files.length === 0) {
                    return;
                }

                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-200';
                    fileItem.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-file text-green-600"></i>
                            <span class="text-sm text-gray-700">${file.name}</span>
                            <span class="text-xs text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                        </div>
                    `;
                    fileList.appendChild(fileItem);
                });
            });
        }

        // Handle "I don't have a middle name" checkbox
        const middleNameInput = document.getElementById('middle_name');
        const noMiddleCheckbox = document.getElementById('no_middle_name');
        
        function handleNoMiddleToggle() {
            if (!middleNameInput || !noMiddleCheckbox) return;
            if (noMiddleCheckbox.checked) {
                middleNameInput.value = '';
                middleNameInput.disabled = true;
                middleNameInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                // Remove required attribute if it exists
                middleNameInput.removeAttribute('required');
            } else {
                middleNameInput.disabled = false;
                middleNameInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        }
        
        if (noMiddleCheckbox) {
            noMiddleCheckbox.addEventListener('change', handleNoMiddleToggle);
            // Check initial state - if middle_name is empty, check the checkbox
            if (middleNameInput && (!middleNameInput.value || middleNameInput.value.trim() === '')) {
                noMiddleCheckbox.checked = false; // Don't auto-check, let user decide
            }
            handleNoMiddleToggle(); // Initial state
        }

        // Middle name validation function
        function validateMiddleName(value) {
            if (!value || !value.trim()) {
                return true; // Empty is allowed (optional field)
            }
            const trimmed = value.trim();
            // Check if it's a single letter
            if (trimmed.length === 1) {
                return false;
            }
            // Check if it's an initial with a period (e.g., "A." or "A. ")
            if (/^[A-Za-z]\.\s*$/.test(trimmed)) {
                return false;
            }
            // Check if it's less than 2 characters after removing periods and spaces
            const cleaned = trimmed.replace(/[.\s]+/g, '');
            if (cleaned.length < 2) {
                return false;
            }
            return true;
        }

        // Add validation on middle name input (only if not disabled)
        if (middleNameInput) {
            middleNameInput.addEventListener('blur', function() {
                if (this.disabled) return; // Skip validation if disabled
                const value = this.value;
                if (!validateMiddleName(value)) {
                    this.setCustomValidity('Please enter your full middle name. Initials are not allowed.');
                    this.classList.add('border-red-500');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                }
            });

            middleNameInput.addEventListener('input', function() {
                if (this.disabled) return; // Skip validation if disabled
                const value = this.value;
                if (validateMiddleName(value)) {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                }
            });
        }

        // Form submission validation and handling
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // If "no middle name" is checked, ensure middle_name is empty
                if (noMiddleCheckbox && noMiddleCheckbox.checked && middleNameInput) {
                    middleNameInput.disabled = false; // Re-enable to include in submission
                    middleNameInput.value = ''; // Clear the value
                }
                
                // Validate middle name before submission (only if not disabled/empty)
                if (middleNameInput && !noMiddleCheckbox?.checked && middleNameInput.value.trim()) {
                    if (!validateMiddleName(middleNameInput.value)) {
                        e.preventDefault();
                        alert('Please enter your full middle name. Initials are not allowed.');
                        middleNameInput.focus();
                        return false;
                    }
                }
            });
        }
    </script>
    <script>
        // Ensure starred fields are required before submitting
        document.addEventListener('DOMContentLoaded', function() {
            const isFormControl = (el) => el && ['INPUT', 'SELECT', 'TEXTAREA'].includes(el.tagName);
            const findControl = (label) => {
                const forId = label.getAttribute('for');
                if (forId) {
                    const byId = document.getElementById(forId);
                    if (isFormControl(byId)) return byId;
                }
                const sibling = label.nextElementSibling;
                if (isFormControl(sibling)) return sibling;
                return label.parentElement ? label.parentElement.querySelector('input, select, textarea') : null;
            };

            document.querySelectorAll('label').forEach((label) => {
                const text = (label.textContent || '').trim();
                const hasStar = label.querySelector('.text-red-500, .text-danger') || text.includes('*');
                if (!hasStar) return;

                const control = findControl(label);
                if (!isFormControl(control)) return;

                control.setAttribute('required', 'required');
                control.setAttribute('aria-required', 'true');

                if (control.type === 'radio' || control.type === 'checkbox') {
                    document.querySelectorAll(`input[name="${control.name}"]`).forEach((peer) => {
                        peer.setAttribute('required', 'required');
                        peer.setAttribute('aria-required', 'true');
                    });
                }
            });
        });
    </script>
    @notifyJs
</body>
</html>
