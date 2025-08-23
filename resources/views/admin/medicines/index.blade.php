@extends('admin.main.layout')

@section('title', 'Medicine Inventory')

@section('content')
<div class="max-w-7xl mx-auto pt-2">
    <!-- Header Section -->
    <div class="mb-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Medicine Inventory</h1>
                <p class="text-sm md:text-base text-gray-600">Manage medicines, stock levels, and dispensing</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.medicines.report') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-file-alt mr-2"></i>
                    Dispense Report
                </a>
                <a href="{{ route('admin.medicines.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Medicine
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-3 bg-green-50 border border-green-200 rounded-lg p-4">
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
        <div class="mb-3 bg-red-50 border border-red-200 rounded-lg p-4">
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

    <!-- Filters -->
    <form method="GET" class="mb-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, generic name, or category..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" />
                </div>
            </div>
            <div class="w-full sm:w-48">
                <select name="category" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">All Categories</option>
                    @foreach(['Antibiotic','Pain Relief','Vitamins','Chronic','Emergency','Antihypertensive','Antidiabetic','Antihistamine','Other'] as $cat)
                        <option value="{{ $cat }}" @selected(request('category')===$cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-48">
                <select name="stock_status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-md">
                    <option value="">Any Stock</option>
                    <option value="low" @selected(request('stock_status')==='low')>Low Stock</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.medicines.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 mb-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-pills text-indigo-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Total Medicines</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['total_medicines'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Low Stock</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['low_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 md:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-yellow-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-500">Expiring Soon</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['expiring_soon'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($medicines->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-pills text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No medicines found</h3>
            <p class="text-gray-500">Get started by adding the first medicine.</p>
            <div class="mt-6">
                <a href="{{ route('admin.medicines.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Add Medicine
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center"><i class="fas fa-prescription-bottle-alt mr-2"></i>Medicine</div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center"><i class="fas fa-tags mr-2"></i>Category</div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center"><i class="fas fa-box mr-2"></i>Stock</div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center"><i class="fas fa-calendar-day mr-2"></i>Expiry</div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center"><i class="fas fa-cogs mr-2"></i>Actions</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($medicines as $med)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $med->name }}</div>
                                    <div class="text-gray-500">{{ $med->generic_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $med->category }}</td>
                            <td class="px-6 py-4">
                                @php $status = $med->stock_status; @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-sm">{{ $med->current_stock }}</span>
                                    <span class="text-xs px-2 py-1 rounded {{ $status==='low' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ ucfirst($status) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if(!$med->expiry_date)
                                    <span class="text-gray-400">N/A</span>
                                @else
                                    {{ $med->expiry_date->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.medicines.edit', $med) }}" class="inline-flex items-center px-2 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($medicines->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $medicines->links() }}
            </div>
            @endif
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @foreach($medicines as $med)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $med->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $med->generic_name }}</p>
                    </div>
                    @php $status = $med->stock_status; @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $status==='low' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    <p><i class="fas fa-tags mr-1 text-gray-400"></i> {{ $med->category }}</p>
                    <p><i class="fas fa-box mr-1 text-gray-400"></i> Stock: <span class="font-medium">{{ $med->current_stock }}</span></p>
                    <p><i class="fas fa-calendar-day mr-1 text-gray-400"></i> Expiry: {{ $med->expiry_date ? $med->expiry_date->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.medicines.edit', $med) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none transition duration-200">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection