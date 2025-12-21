@extends('admin.main.layout')

@section('title', 'Template Builder - ' . $template->document_type)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Template Builder</h1>
                <p class="text-gray-600">{{ $template->document_type }} - Visual template editor</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button id="validateBtn" class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 bg-white hover:bg-blue-50">
                    <i class="fas fa-check-circle mr-2"></i>
                    Validate
                </button>
                <button id="saveBtn" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i>
                    Save Template
                </button>
                <a href="{{ route('admin.templates.edit', $template) }}" 
                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Template Builder -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Sidebar - Placeholders and Tools -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Placeholders</h3>
                
                <!-- Search Placeholders -->
                <div class="mb-4">
                    <input 
                        type="text" 
                        id="placeholderSearch" 
                        placeholder="Search placeholders..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    >
                </div>

                <!-- Placeholder List -->
                <div id="placeholderList" class="space-y-2 max-h-96 overflow-y-auto">
                    @php
                        $validPlaceholders = \App\Services\TemplateDefaultsService::getValidPlaceholders();
                    @endphp
                    @foreach($validPlaceholders as $key => $info)
                        <div class="placeholder-item p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer" 
                             data-placeholder="[{{ $key }}]"
                             data-description="{{ $info['description'] ?? '' }}">
                            <div class="flex items-center justify-between">
                                <code class="text-sm font-mono text-blue-600">[{{ $key }}]</code>
                                <button class="insert-placeholder text-xs text-green-600 hover:text-green-700" data-placeholder="[{{ $key }}]">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $info['label'] ?? $key }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <button id="previewBtn" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Template
                        </button>
                        <button id="testBtn" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-vial mr-2"></i>
                            Test Template
                        </button>
                        <a href="{{ route('admin.templates.test', $template) }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-flask mr-2"></i>
                            Run Full Tests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Editor Area -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button class="tab-btn active px-6 py-3 text-sm font-medium text-green-600 border-b-2 border-green-600" data-tab="header">
                            Header
                        </button>
                        <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="body">
                            Body
                        </button>
                        <button class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="footer">
                            Footer
                        </button>
                    </nav>
                </div>

                <!-- Editor Content -->
                <div class="p-6">
                    <!-- Header Editor -->
                    <div id="headerTab" class="tab-content">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Header Content</label>
                        <textarea 
                            id="headerContent" 
                            class="template-editor block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" 
                            rows="10"
                        >{{ old('header_content', $template->header_content) }}</textarea>
                    </div>

                    <!-- Body Editor -->
                    <div id="bodyTab" class="tab-content hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Body Content <span class="text-red-500">*</span></label>
                        <textarea 
                            id="bodyContent" 
                            class="template-editor block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" 
                            rows="15"
                            required
                        >{{ old('body_content', $template->body_content) }}</textarea>
                    </div>

                    <!-- Footer Editor -->
                    <div id="footerTab" class="tab-content hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Footer Content</label>
                        <textarea 
                            id="footerContent" 
                            class="template-editor block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" 
                            rows="10"
                        >{{ old('footer_content', $template->footer_content) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Live Preview -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-700">Live Preview</h3>
                    <button id="refreshPreview" class="text-xs text-gray-600 hover:text-gray-800">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Refresh
                    </button>
                </div>
                <div class="p-6">
                    <iframe 
                        id="previewFrame" 
                        class="w-full border border-gray-200 rounded" 
                        style="min-height: 500px;"
                        srcdoc=""
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Modal -->
<div id="validationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Template Validation</h3>
            <button id="closeValidation" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="validationResults"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-green-600', 'border-green-600');
                b.classList.add('text-gray-500');
            });
            this.classList.add('active', 'text-green-600', 'border-green-600');
            this.classList.remove('text-gray-500');
            
            // Update content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(tab + 'Tab').classList.remove('hidden');
        });
    });

    // Insert placeholder
    document.querySelectorAll('.insert-placeholder').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const placeholder = this.dataset.placeholder;
            const activeTab = document.querySelector('.tab-content:not(.hidden)');
            const textarea = activeTab.querySelector('textarea');
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);
            textarea.value = textBefore + placeholder + textAfter;
            textarea.focus();
            textarea.setSelectionRange(cursorPos + placeholder.length, cursorPos + placeholder.length);
            updatePreview();
        });
    });

    // Placeholder search
    document.getElementById('placeholderSearch').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.placeholder-item').forEach(item => {
            const placeholder = item.dataset.placeholder.toLowerCase();
            const description = (item.dataset.description || '').toLowerCase();
            if (placeholder.includes(search) || description.includes(search)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Update preview
    function updatePreview() {
        const header = document.getElementById('headerContent').value;
        const body = document.getElementById('bodyContent').value;
        const footer = document.getElementById('footerContent').value;
        
        // Sample data for preview
        const sampleData = {
            resident_name: 'Juan Dela Cruz',
            resident_address: '123 Sample Street',
            day: '25th',
            month: 'February',
            year: '2025',
            barangay_name: 'Lower Malinao',
            municipality_name: 'Padada',
            province_name: 'Davao Del Sur',
            prepared_by_name: 'Sample Secretary',
            captain_name: 'Hon. Sample Captain'
        };
        
        // Simple placeholder replacement for preview
        let previewHtml = header + body + footer;
        Object.keys(sampleData).forEach(key => {
            previewHtml = previewHtml.replace(new RegExp('\\[' + key + '\\]', 'g'), sampleData[key]);
        });
        
        // Wrap in basic HTML structure
        const fullHtml = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "Times New Roman", serif; padding: 20px; }
    </style>
</head>
<body>
    ${previewHtml}
</body>
</html>`;
        
        document.getElementById('previewFrame').srcdoc = fullHtml;
    }

    // Auto-update preview on input
    document.querySelectorAll('.template-editor').forEach(textarea => {
        textarea.addEventListener('input', updatePreview);
    });

    // Initial preview
    updatePreview();

    // Refresh preview button
    document.getElementById('refreshPreview').addEventListener('click', updatePreview);

    // Validate button
    document.getElementById('validateBtn').addEventListener('click', function() {
        const header = document.getElementById('headerContent').value;
        const body = document.getElementById('bodyContent').value;
        const footer = document.getElementById('footerContent').value;
        
        fetch('{{ route("admin.templates.validate", $template) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                header_content: header,
                body_content: body,
                footer_content: footer
            })
        })
        .then(response => response.json())
        .then(data => {
            let html = '<div class="space-y-4">';
            
            if (data.valid) {
                html += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
                html += '<div class="flex items-center"><i class="fas fa-check-circle text-green-600 mr-2"></i>';
                html += '<span class="text-green-800 font-medium">Template is valid!</span></div></div>';
            } else {
                html += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
                html += '<div class="flex items-center mb-2"><i class="fas fa-exclamation-circle text-red-600 mr-2"></i>';
                html += '<span class="text-red-800 font-medium">Template has issues</span></div>';
                if (data.validation.errors && data.validation.errors.length > 0) {
                    html += '<ul class="list-disc list-inside text-sm text-red-700 mt-2">';
                    data.validation.errors.forEach(error => {
                        html += `<li>${error}</li>`;
                    });
                    html += '</ul>';
                }
                html += '</div>';
            }
            
            if (data.unreplaced_placeholders && data.unreplaced_placeholders.length > 0) {
                html += '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
                html += '<div class="text-sm font-medium text-yellow-800 mb-2">Unreplaced Placeholders:</div>';
                html += '<div class="flex flex-wrap gap-2">';
                data.unreplaced_placeholders.forEach(ph => {
                    html += `<code class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">[${ph}]</code>`;
                });
                html += '</div></div>';
            }
            
            if (data.warnings && data.warnings.length > 0) {
                html += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
                html += '<div class="text-sm font-medium text-blue-800 mb-2">Warnings:</div>';
                html += '<ul class="list-disc list-inside text-sm text-blue-700">';
                data.warnings.forEach(warning => {
                    html += `<li>${warning}</li>`;
                });
                html += '</ul></div>';
            }
            
            html += '</div>';
            
            document.getElementById('validationResults').innerHTML = html;
            document.getElementById('validationModal').classList.remove('hidden');
            document.getElementById('validationModal').classList.add('flex');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error validating template');
        });
    });

    // Close validation modal
    document.getElementById('closeValidation').addEventListener('click', function() {
        document.getElementById('validationModal').classList.add('hidden');
        document.getElementById('validationModal').classList.remove('flex');
    });

    // Save button
    document.getElementById('saveBtn').addEventListener('click', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.templates.update", $template) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PUT';
        form.appendChild(method);
        
        const header = document.createElement('input');
        header.type = 'hidden';
        header.name = 'header_content';
        header.value = document.getElementById('headerContent').value;
        form.appendChild(header);
        
        const body = document.createElement('input');
        body.type = 'hidden';
        body.name = 'body_content';
        body.value = document.getElementById('bodyContent').value;
        form.appendChild(body);
        
        const footer = document.createElement('input');
        footer.type = 'hidden';
        footer.name = 'footer_content';
        footer.value = document.getElementById('footerContent').value;
        form.appendChild(footer);
        
        document.body.appendChild(form);
        form.submit();
    });

    // Test button
    document.getElementById('testBtn').addEventListener('click', function() {
        window.location.href = '{{ route("admin.templates.test", $template) }}';
    });
});
</script>
@endpush

