@extends('admin.main.layout')

@section('title', $mode === 'create' ? 'Create FAQ' : 'Edit FAQ')

@section('content')
<div class="max-w-4xl mx-auto pt-2">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $mode === 'create' ? 'Create New FAQ' : 'Edit FAQ' }}</h1>
        <a href="{{ route('admin.faqs.index') }}" class="bg-gray-600 text-white rounded-lg px-5 py-2 hover:bg-gray-700 font-semibold flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Back to FAQs
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
            <h4 class="font-medium mb-2">Please fix the following errors:</h4>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $mode === 'create' ? route('admin.faqs.store') : route('admin.faqs.update', $faq) }}" class="bg-white rounded-lg shadow-sm border p-6">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Question -->
            <div class="md:col-span-2">
                <label for="question" class="block text-sm font-medium text-gray-700 mb-2">Question *</label>
                <input type="text" 
                       id="question" 
                       name="question" 
                       value="{{ old('question', $faq->question ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 @error('question') border-red-500 @enderror"
                       placeholder="Enter the question..."
                       required>
                @error('question')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <input type="text" 
                       id="category" 
                       name="category" 
                       value="{{ old('category', $faq->category ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 @error('category') border-red-500 @enderror"
                       placeholder="Example: General, How-to Guides, Hotlines"
                       list="category-suggestions"
                       required>
                <datalist id="category-suggestions">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                    <option value="General">
                    <option value="How-to Guides">
                    <option value="Hotlines/Contacts">
                    <option value="Document Requests">
                    <option value="Account Issues">
                </datalist>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active (visible to residents)
                    </label>
                </div>
            </div>
        </div>

        <!-- Answer -->
        <div class="mt-6">
            <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">Answer *</label>
            <textarea id="answer" 
                      name="answer" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 @error('answer') border-red-500 @enderror"
                      rows="8"
                      placeholder="Enter the detailed answer...">{!! old('answer', $faq->answer ?? '') !!}</textarea>
            @error('answer')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.faqs.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    id="submitBtn"
                    class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition duration-200 flex items-center">
                <i class="fas fa-save mr-2"></i>
                <span id="submitText">{{ $mode === 'create' ? 'Create FAQ' : 'Update FAQ' }}</span>
            </button>
        </div>
    </form>
    <div class="mt-4 text-sm text-gray-500">
        <i class="fas fa-info-circle mr-1 text-gray-400"></i>To change FAQ order, use the drag-and-drop list on the main FAQ Management page.
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for the answer field
    ClassicEditor
        .create(document.querySelector('#answer'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },
            // Ensure HTML content is properly handled
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        })
        .then(editor => {
            console.log('CKEditor initialized successfully');
            
            // Ensure the editor content is properly set
            const initialContent = document.querySelector('#answer').value;
            if (initialContent) {
                editor.setData(initialContent);
            }
            
            // Ensure form submission includes CKEditor content
            document.querySelector('form').addEventListener('submit', function(e) {
                const editorData = editor.getData().trim();
                if (!editorData || editorData === '<p>&nbsp;</p>' || editorData === '<p></p>') {
                    e.preventDefault();
                    alert('Please enter the answer content.');
                    return false;
                }
                document.querySelector('#answer').value = editorData;
                // Show loading state
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                submitBtn.disabled = true;
                submitText.textContent = '{{ $mode === 'create' ? 'Creating...' : 'Updating...' }}';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span id="submitText">' + submitText.textContent + '</span>';
            });
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
});
</script>
@endpush
