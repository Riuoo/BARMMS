@extends('admin.main.layout')

@section('title', 'Word-Style Template Editor - ' . $template->document_type)

@section('content')
<div class="h-screen flex flex-col bg-gray-100">
    <!-- Word-Style Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <!-- Title Bar -->
        <div class="flex items-center justify-between px-4 py-2 bg-blue-600 text-white">
            <div class="flex items-center space-x-3">
                <i class="fas fa-file-word text-xl"></i>
                <span class="font-medium">{{ $template->document_type }} - Template Editor</span>
            </div>
            <div class="flex items-center space-x-2">
                <button id="saveBtn" class="px-3 py-1 bg-green-600 hover:bg-green-700 rounded text-sm">
                    <i class="fas fa-save mr-1"></i>Save
                </button>
                <button id="previewBtn" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 rounded text-sm">
                    <i class="fas fa-eye mr-1"></i>Preview
                </button>
                <a href="{{ route('admin.templates.index') }}" class="px-3 py-1 bg-gray-600 hover:bg-gray-700 rounded text-sm">
                    <i class="fas fa-times mr-1"></i>Close
                </a>
            </div>
        </div>

        <!-- Ribbon Toolbar -->
        <div class="bg-white border-b border-gray-200">
            <!-- Ribbon Tabs -->
            <div class="flex border-b border-gray-200">
                <button class="ribbon-tab active px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="home">
                    <i class="fas fa-home mr-1"></i>Home
                </button>
                <button class="ribbon-tab px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600" data-tab="insert">
                    <i class="fas fa-plus mr-1"></i>Insert
                </button>
                <button class="ribbon-tab px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600" data-tab="layout">
                    <i class="fas fa-columns mr-1"></i>Layout
                </button>
                <button class="ribbon-tab px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600" data-tab="review">
                    <i class="fas fa-eye mr-1"></i>Review
                </button>
            </div>

            <!-- Ribbon Content -->
            <div class="p-4">
                <!-- Home Tab -->
                <div id="home-tab" class="ribbon-content active">
                    <div class="grid grid-cols-4 gap-6">
                        <!-- Clipboard Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clipboard</h4>
                            <div class="flex space-x-1">
                                <button class="ribbon-btn" onclick="document.execCommand('paste')">
                                    <i class="fas fa-paste"></i>
                                    <span class="text-xs">Paste</span>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('cut')">
                                    <i class="fas fa-cut"></i>
                                    <span class="text-xs">Cut</span>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('copy')">
                                    <i class="fas fa-copy"></i>
                                    <span class="text-xs">Copy</span>
                                </button>
                            </div>
                        </div>

                        <!-- Font Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Font</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <select id="fontFamily" class="ribbon-select text-xs">
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Calibri">Calibri</option>
                                    <option value="Georgia">Georgia</option>
                                </select>
                                <select id="fontSize" class="ribbon-select text-xs">
                                    <option value="8">8</option>
                                    <option value="10">10</option>
                                    <option value="12" selected>12</option>
                                    <option value="14">14</option>
                                    <option value="16">16</option>
                                    <option value="18">18</option>
                                    <option value="20">20</option>
                                    <option value="24">24</option>
                                </select>
                            </div>
                            <div class="flex space-x-1">
                                <button class="ribbon-btn" onclick="document.execCommand('bold')">
                                    <i class="fas fa-bold"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('italic')">
                                    <i class="fas fa-italic"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('underline')">
                                    <i class="fas fa-underline"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('strikeThrough')">
                                    <i class="fas fa-strikethrough"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Paragraph Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Paragraph</h4>
                            <div class="flex space-x-1">
                                <button class="ribbon-btn" onclick="document.execCommand('justifyLeft')">
                                    <i class="fas fa-align-left"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('justifyCenter')">
                                    <i class="fas fa-align-center"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('justifyRight')">
                                    <i class="fas fa-align-right"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('justifyFull')">
                                    <i class="fas fa-align-justify"></i>
                                </button>
                            </div>
                            <div class="flex space-x-1">
                                <button class="ribbon-btn" onclick="document.execCommand('insertUnorderedList')">
                                    <i class="fas fa-list-ul"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('insertOrderedList')">
                                    <i class="fas fa-list-ol"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('outdent')">
                                    <i class="fas fa-outdent"></i>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('indent')">
                                    <i class="fas fa-indent"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Styles Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Styles</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="document.execCommand('formatBlock', false, 'h1')">
                                    <span class="text-xs font-bold">H1</span>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('formatBlock', false, 'h2')">
                                    <span class="text-xs font-bold">H2</span>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('formatBlock', false, 'h3')">
                                    <span class="text-xs font-bold">H3</span>
                                </button>
                                <button class="ribbon-btn" onclick="document.execCommand('formatBlock', false, 'p')">
                                    <span class="text-xs">Normal</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Insert Tab -->
                <div id="insert-tab" class="ribbon-content hidden">
                    <div class="grid grid-cols-3 gap-6">
                        <!-- Placeholders Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Placeholders</h4>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($template->getAvailablePlaceholders() as $key => $description)
                                    <button class="placeholder-btn ribbon-btn text-xs" data-placeholder="{{ $key }}" title="{{ $description }}">
                                        <span class="font-mono text-green-600">[{{ $key }}]</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Quick Elements Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Quick Elements</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="insertHeader()">
                                    <i class="fas fa-heading mr-1"></i>
                                    <span class="text-xs">Header</span>
                                </button>
                                <button class="ribbon-btn" onclick="insertSignature()">
                                    <i class="fas fa-signature mr-1"></i>
                                    <span class="text-xs">Signature</span>
                                </button>
                                <button class="ribbon-btn" onclick="insertDate()">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span class="text-xs">Date</span>
                                </button>
                                <button class="ribbon-btn" onclick="insertTable()">
                                    <i class="fas fa-table mr-1"></i>
                                    <span class="text-xs">Table</span>
                                </button>
                            </div>
                        </div>

                        <!-- Special Characters Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Special</h4>
                            <div class="grid grid-cols-3 gap-1">
                                <button class="ribbon-btn" onclick="insertSpecialChar('©')">©</button>
                                <button class="ribbon-btn" onclick="insertSpecialChar('®')">®</button>
                                <button class="ribbon-btn" onclick="insertSpecialChar('™')">™</button>
                                <button class="ribbon-btn" onclick="insertSpecialChar('°')">°</button>
                                <button class="ribbon-btn" onclick="insertSpecialChar('±')">±</button>
                                <button class="ribbon-btn" onclick="insertSpecialChar('×')">×</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layout Tab -->
                <div id="layout-tab" class="ribbon-content hidden">
                    <div class="grid grid-cols-3 gap-6">
                        <!-- Page Setup Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Page Setup</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <select id="pageSize" class="ribbon-select text-xs">
                                    <option value="A4">A4</option>
                                    <option value="Letter">Letter</option>
                                    <option value="Legal">Legal</option>
                                </select>
                                <select id="orientation" class="ribbon-select text-xs">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape">Landscape</option>
                                </select>
                            </div>
                        </div>

                        <!-- Margins Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Margins</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="setMargins('normal')">
                                    <span class="text-xs">Normal</span>
                                </button>
                                <button class="ribbon-btn" onclick="setMargins('narrow')">
                                    <span class="text-xs">Narrow</span>
                                </button>
                                <button class="ribbon-btn" onclick="setMargins('wide')">
                                    <span class="text-xs">Wide</span>
                                </button>
                                <button class="ribbon-btn" onclick="setMargins('custom')">
                                    <span class="text-xs">Custom</span>
                                </button>
                            </div>
                        </div>

                        <!-- Spacing Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Spacing</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="setLineSpacing(1)">
                                    <span class="text-xs">1.0</span>
                                </button>
                                <button class="ribbon-btn" onclick="setLineSpacing(1.5)">
                                    <span class="text-xs">1.5</span>
                                </button>
                                <button class="ribbon-btn" onclick="setLineSpacing(2)">
                                    <span class="text-xs">2.0</span>
                                </button>
                                <button class="ribbon-btn" onclick="setLineSpacing(2.5)">
                                    <span class="text-xs">2.5</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Tab -->
                <div id="review-tab" class="ribbon-content hidden">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Proofing Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">Proofing</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="spellCheck()">
                                    <i class="fas fa-spell-check mr-1"></i>
                                    <span class="text-xs">Spell Check</span>
                                </button>
                                <button class="ribbon-btn" onclick="validateTemplate()">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <span class="text-xs">Validate</span>
                                </button>
                            </div>
                        </div>

                        <!-- View Group -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">View</h4>
                            <div class="grid grid-cols-2 gap-1">
                                <button class="ribbon-btn" onclick="toggleRulers()">
                                    <i class="fas fa-ruler mr-1"></i>
                                    <span class="text-xs">Rulers</span>
                                </button>
                                <button class="ribbon-btn" onclick="toggleGrid()">
                                    <i class="fas fa-th mr-1"></i>
                                    <span class="text-xs">Grid</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Area -->
    <div class="flex-1 flex">
        <!-- Rulers -->
        <div class="bg-white border-r border-gray-200">
            <div id="verticalRuler" class="w-8 h-full bg-gray-50 border-b border-gray-200"></div>
        </div>
        
        <!-- Main Document -->
        <div class="flex-1 flex flex-col">
            <!-- Horizontal Ruler -->
            <div id="horizontalRuler" class="h-8 bg-gray-50 border-b border-gray-200"></div>
            
            <!-- Document Content -->
            <div class="flex-1 bg-white shadow-inner overflow-auto">
                <div class="max-w-4xl mx-auto p-8 bg-white min-h-full">
                    <div id="documentEditor" 
                         class="prose prose-lg max-w-none min-h-[800px] outline-none"
                         contenteditable="true"
                         style="font-family: 'Times New Roman', serif; line-height: 1.6;">
                        {!! $template->header_content !!}
                        {!! $template->body_content !!}
                        {!! $template->footer_content !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="bg-gray-100 border-t border-gray-200 px-4 py-2 text-sm text-gray-600">
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <span>Words: <span id="wordCount">0</span></span>
                    <span>Characters: <span id="charCount">0</span></span>
                    <span>Pages: <span id="pageCount">1</span></span>
                </div>
                <div class="flex space-x-4">
                    <span>Zoom: <span id="zoomLevel">100%</span></span>
                    <span id="cursorPosition">Line 1, Col 1</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Form (Hidden) -->
<form id="saveForm" action="{{ route('admin.templates.update', $template) }}" method="POST" class="hidden">
    @csrf
    @method('PUT')
    <input type="hidden" id="header_content" name="header_content" value="{{ $template->header_content }}">
    <input type="hidden" id="body_content" name="body_content" value="{{ $template->body_content }}">
    <input type="hidden" id="footer_content" name="footer_content" value="{{ $template->footer_content }}">
</form>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Document Preview</h3>
            <button id="closePreview" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="previewContent" class="border border-gray-200 rounded-lg p-6 bg-white" style="min-height: 600px;">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.ribbon-btn {
    @apply px-2 py-1 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-gray-700 text-xs flex flex-col items-center justify-center transition-colors;
}

.ribbon-btn:hover {
    @apply bg-gray-200;
}

.ribbon-btn.active {
    @apply bg-blue-100 border-blue-300 text-blue-700;
}

.ribbon-select {
    @apply px-2 py-1 bg-white border border-gray-300 rounded text-gray-700;
}

.ribbon-tab {
    @apply transition-colors;
}

.ribbon-tab.active {
    @apply text-blue-600 border-blue-600;
}

.ribbon-content {
    @apply transition-all duration-200;
}

.placeholder-btn {
    @apply text-xs;
}

#documentEditor {
    @apply focus:outline-none;
}

#documentEditor:focus {
    @apply outline-none;
}

/* Word-like styling */
.prose {
    @apply text-gray-900;
}

.prose h1 {
    @apply text-2xl font-bold mb-4;
}

.prose h2 {
    @apply text-xl font-bold mb-3;
}

.prose h3 {
    @apply text-lg font-bold mb-2;
}

.prose p {
    @apply mb-3;
}

.prose ul, .prose ol {
    @apply mb-3 pl-6;
}

.prose li {
    @apply mb-1;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('documentEditor');
    const saveBtn = document.getElementById('saveBtn');
    const previewBtn = document.getElementById('previewBtn');
    const ribbonTabs = document.querySelectorAll('.ribbon-tab');
    const ribbonContents = document.querySelectorAll('.ribbon-content');
    const placeholderBtns = document.querySelectorAll('.placeholder-btn');

    // Ribbon tab switching
    ribbonTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Update active tab
            ribbonTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding content
            ribbonContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            
            document.getElementById(targetTab + '-tab').classList.remove('hidden');
            document.getElementById(targetTab + '-tab').classList.add('active');
        });
    });

    // Placeholder insertion
    placeholderBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const placeholder = this.dataset.placeholder;
            insertText(`[${placeholder}]`);
        });
    });

    // Font controls
    document.getElementById('fontFamily').addEventListener('change', function() {
        document.execCommand('fontName', false, this.value);
    });

    document.getElementById('fontSize').addEventListener('change', function() {
        document.execCommand('fontSize', false, this.value);
    });

    // Word count and character count
    function updateCounts() {
        const text = editor.innerText || editor.textContent;
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const chars = text.length;
        
        document.getElementById('wordCount').textContent = words;
        document.getElementById('charCount').textContent = chars;
    }

    editor.addEventListener('input', updateCounts);
    editor.addEventListener('keyup', updateCounts);
    updateCounts();

    // Cursor position tracking
    editor.addEventListener('keyup', updateCursorPosition);
    editor.addEventListener('click', updateCursorPosition);
    editor.addEventListener('mouseup', updateCursorPosition);

    function updateCursorPosition() {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(editor);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            const text = preCaretRange.toString();
            const lines = text.split('\n');
            const currentLine = lines.length;
            const currentCol = lines[lines.length - 1].length + 1;
            
            document.getElementById('cursorPosition').textContent = `Line ${currentLine}, Col ${currentCol}`;
        }
    }

    // Save functionality
    saveBtn.addEventListener('click', function() {
        const content = editor.innerHTML;
        
        // Split content into header, body, and footer (simple implementation)
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        
        // For now, put everything in body content
        document.getElementById('body_content').value = content;
        document.getElementById('header_content').value = '';
        document.getElementById('footer_content').value = '';
        
        document.getElementById('saveForm').submit();
    });

    // Preview functionality
    previewBtn.addEventListener('click', function() {
        const content = editor.innerHTML;
        
        // Sample data for preview
        const sampleData = {
            'resident_name': 'Juan Dela Cruz',
            'resident_address': '123 Sample Street, Barangay Sample',
            'civil_status': 'Married',
            'purpose': 'employment purposes',
            'barangay_name': 'Sample Barangay',
            'municipality_name': 'Sample Municipality',
            'province_name': 'Sample Province',
            'official_name': 'Hon. Sample Official',
            'official_position': 'Barangay Captain',
            'current_date': 'January 15, 2024'
        };

        // Replace placeholders with sample data
        let previewContent = content;
        Object.entries(sampleData).forEach(([key, value]) => {
            previewContent = previewContent.replace(new RegExp(`\\[${key}\\]`, 'g'), value);
        });

        document.getElementById('previewContent').innerHTML = `
            <style>
                body { font-family: "Times New Roman", serif; line-height: 1.6; color: #333; }
            </style>
            ${previewContent}
        `;
        document.getElementById('previewModal').classList.remove('hidden');
    });

    document.getElementById('closePreview').addEventListener('click', function() {
        document.getElementById('previewModal').classList.add('hidden');
    });

    // Close modal when clicking outside
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Utility functions
    function insertText(text) {
        document.execCommand('insertText', false, text);
    }

    function insertSpecialChar(char) {
        insertText(char);
    }

    function insertHeader() {
        const headerContent = `
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">REPUBLIC OF THE PHILIPPINES</h1>
                <h2 style="font-size: 16px; margin-bottom: 5px;">Province of [province_name]</h2>
                <h2 style="font-size: 16px; margin-bottom: 5px;">Municipality of [municipality_name]</h2>
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">BARANGAY [barangay_name]</h1>
            </div>
        `;
        document.execCommand('insertHTML', false, headerContent);
    }

    function insertSignature() {
        const signatureContent = `
            <div style="margin-top: 50px; text-align: right;">
                <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; margin-bottom: 10px;"></div>
                <p style="font-weight: bold; margin-bottom: 5px;">[official_name]</p>
                <p style="font-size: 14px; color: #666;">[official_position]</p>
            </div>
        `;
        document.execCommand('insertHTML', false, signatureContent);
    }

    function insertDate() {
        const dateContent = `
            <p>Issued this [current_date] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
        `;
        document.execCommand('insertHTML', false, dateContent);
    }

    function insertTable() {
        const tableContent = `
            <table border="1" style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #000;">Header 1</td>
                    <td style="padding: 8px; border: 1px solid #000;">Header 2</td>
                    <td style="padding: 8px; border: 1px solid #000;">Header 3</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000;">Data 1</td>
                    <td style="padding: 8px; border: 1px solid #000;">Data 2</td>
                    <td style="padding: 8px; border: 1px solid #000;">Data 3</td>
                </tr>
            </table>
        `;
        document.execCommand('insertHTML', false, tableContent);
    }

    function setMargins(type) {
        // Implementation for setting margins
        console.log('Setting margins:', type);
    }

    function setLineSpacing(spacing) {
        editor.style.lineHeight = spacing;
    }

    function spellCheck() {
        // Implementation for spell checking
        alert('Spell check feature would be implemented here');
    }

    function validateTemplate() {
        // Implementation for template validation
        alert('Template validation would be implemented here');
    }

    function toggleRulers() {
        const verticalRuler = document.getElementById('verticalRuler');
        const horizontalRuler = document.getElementById('horizontalRuler');
        
        if (verticalRuler.style.display === 'none') {
            verticalRuler.style.display = 'block';
            horizontalRuler.style.display = 'block';
        } else {
            verticalRuler.style.display = 'none';
            horizontalRuler.style.display = 'none';
        }
    }

    function toggleGrid() {
        // Implementation for grid toggle
        console.log('Grid toggle');
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 's':
                    e.preventDefault();
                    saveBtn.click();
                    break;
                case 'p':
                    e.preventDefault();
                    previewBtn.click();
                    break;
            }
        }
    });
});
</script>
@endpush
