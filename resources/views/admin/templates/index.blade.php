@extends('admin.main.layout')

@section('title', 'Document Templates')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Templates</h1>
                <p class="text-gray-600">Manage and customize document templates for residents</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Template
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search Templates</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search by document type, description..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="categoryFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Categories</option>
                    <option value="certificates">Certificates</option>
                    <option value="clearances">Clearances</option>
                    <option value="permits">Permits</option>
                    <option value="identifications">Identifications</option>
                    <option value="reports">Reports</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
                <button id="showAllBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-list mr-1"></i>
                    Show All
                </button>
                <button id="showActiveBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-check-circle mr-1"></i>
                    Active Only
                </button>
                <button id="showRecentBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-clock mr-1"></i>
                    Recently Updated
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Templates</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalTemplates">{{ $templates->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Templates</p>
                    <p class="text-2xl font-semibold text-gray-900" id="activeTemplates">{{ $templates->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recently Updated</p>
                    <p class="text-2xl font-semibold text-gray-900" id="recentTemplates">{{ $templates->where('updated_at', '>=', now()->subDays(7))->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-certificate text-purple-600 text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Most Used</p>
                    <p class="text-2xl font-semibold text-gray-900" id="mostUsedTemplate">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div id="templatesContainer">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="template-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200" 
                     data-template-type="{{ strtolower($template->document_type) }}"
                     data-template-status="{{ $template->is_active ? 'active' : 'inactive' }}"
                     data-template-updated="{{ $template->updated_at->timestamp }}">
                    <div class="p-6">
                        <!-- Template Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-green-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 template-title">{{ $template->document_type }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Template Info -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar mr-2"></i>
                                <span>Updated: {{ $template->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-tags mr-2"></i>
                                <span>Placeholders: {{ count($template->placeholders ?? []) }}</span>
                            </div>
                            @if($template->description)
                                <div class="text-sm text-gray-600">
                                    <p class="line-clamp-2">{{ $template->description }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.templates.edit', $template) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-file-word mr-1"></i>
                                Edit
                            </a>
                            
                            <a href="{{ route('admin.templates.word-integration', $template) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                                <i class="fas fa-download mr-1"></i>
                                Word Doc
                            </a>

                            <button type="button" 
                                    onclick="previewTemplate({{ $template->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                                <i class="fas fa-eye mr-1"></i>
                                Preview
                            </button>

                            <form action="{{ route('admin.templates.reset', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to reset this template to default?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-200">
                                    <i class="fas fa-undo mr-1"></i>
                                    Reset
                                </button>
                            </form>

                            <form action="{{ route('admin.templates.toggle-status', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                                    <i class="fas {{ $template->is_active ? 'fa-toggle-on text-green-600' : 'fa-toggle-off text-gray-400' }} mr-1"></i>
                                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No templates found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first template using our easy-to-use wizard.</p>
                    <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Template
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden text-center py-12">
        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-search text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No templates found</h3>
        <p class="text-gray-500">Try adjusting your search criteria or filters.</p>
        <button id="clearFilters" class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
            <i class="fas fa-times mr-2"></i>
            Clear Filters
        </button>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Template Preview</h3>
                <button id="closePreview" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="previewContent" class="border border-gray-200 rounded-lg p-6 bg-white" style="min-height: 500px;">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const templatesContainer = document.getElementById('templatesContainer');
    const noResults = document.getElementById('noResults');
    const templateCards = document.querySelectorAll('.template-card');

    // Search and filter functionality
    function filterTemplates() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();
        let visibleCount = 0;

        templateCards.forEach(card => {
            const title = card.querySelector('.template-title').textContent.toLowerCase();
            const templateType = card.dataset.templateType;
            const templateStatus = card.dataset.templateStatus;
            const updated = parseInt(card.dataset.templateUpdated);

            let show = true;

            // Search filter
            if (searchTerm && !title.includes(searchTerm)) {
                show = false;
            }

            // Category filter
            if (category && !templateType.includes(category)) {
                show = false;
            }

            // Status filter
            if (status && templateStatus !== status) {
                show = false;
            }

            if (show) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            templatesContainer.style.display = 'none';
            noResults.classList.remove('hidden');
        } else {
            templatesContainer.style.display = 'block';
            noResults.classList.add('hidden');
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterTemplates);
    categoryFilter.addEventListener('change', filterTemplates);
    statusFilter.addEventListener('change', filterTemplates);

    // Quick action buttons
    document.getElementById('showAllBtn').addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        statusFilter.value = '';
        filterTemplates();
    });

    document.getElementById('showActiveBtn').addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        statusFilter.value = 'active';
        filterTemplates();
    });

    document.getElementById('showRecentBtn').addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        statusFilter.value = '';
        
        const oneWeekAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
        
        templateCards.forEach(card => {
            const updated = parseInt(card.dataset.templateUpdated);
            if (updated >= oneWeekAgo) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        templatesContainer.style.display = 'block';
        noResults.classList.add('hidden');
    });

    document.getElementById('clearFilters').addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        statusFilter.value = '';
        filterTemplates();
    });

    // Preview functionality
    window.previewTemplate = function(templateId) {
        // Show loading state
        document.getElementById('previewContent').innerHTML = '<div class="flex items-center justify-center h-64"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
        document.getElementById('previewModal').classList.remove('hidden');

        // Fetch template preview
        fetch(`/admin/templates/${templateId}/preview`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('previewContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('previewContent').innerHTML = '<div class="text-center text-red-600">Error loading preview</div>';
            });
    };

    document.getElementById('closePreview').addEventListener('click', function() {
        document.getElementById('previewModal').classList.add('hidden');
    });

    // Close modal when clicking outside
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush 