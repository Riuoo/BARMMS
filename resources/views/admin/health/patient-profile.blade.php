@php
    use Illuminate\Support\Str;
@endphp
@extends('admin.main.layout')

@section('title', 'Patient Health Profile')

@section('content')
@php
    $displayPurok = $resident->purok;
    if (empty($displayPurok) && !empty($resident->address)) {
        $maybeMatch = \Illuminate\Support\Str::of($resident->address)->match('/Purok\\s*\\d+/i');
        $displayPurok = $maybeMatch ?: $resident->address;
    }
@endphp
<div class="max-w-7xl mx-auto pt-2" x-data="{ tab: 'overview' }">
    <!-- Header -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $patientName ?: 'Patient' }}</h1>
            <p class="text-sm text-gray-600">Consolidated health profile and history</p>
            <div class="mt-2 text-sm text-gray-700 space-x-3">
                <span class="inline-flex items-center"><i class="fas fa-envelope mr-2 text-gray-400"></i>{{ $resident->email ?? 'No email' }}</span>
                @if(!empty($resident->contact_number))
                    <span class="inline-flex items-center"><i class="fas fa-phone mr-2 text-gray-400"></i>{{ $resident->contact_number }}</span>
                @endif
                @if(!empty($displayPurok))
                    <span class="inline-flex items-center"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ \Illuminate\Support\Str::startsWith($displayPurok, 'Purok') ? $displayPurok : 'Purok ' . $displayPurok }}</span>
                @endif
            </div>
        </div>
        <div class="mt-3 sm:mt-0 flex space-x-2">
            <button type="button" id="openPatientInfoModal" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-200 border border-gray-200">
                <i class="fas fa-id-card mr-2"></i> Patient Info
            </button>
            <a href="{{ route('admin.medical-records.create') }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                <i class="fas fa-stethoscope mr-2"></i> New Consultation
            </a>
        </div>
    </div>

    <!-- Quick stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center"><i class="fas fa-stethoscope"></i></div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500">Consultations</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['total_consultations'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center"><i class="fas fa-pills"></i></div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500">Medicine Requests</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['total_requests'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 px-4">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button @click="tab='overview'" :class="tab === 'overview' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium">Overview</button>
                <button @click="tab='consultations'" :class="tab === 'consultations' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium">Consultations</button>
                <button @click="tab='requests'" :class="tab === 'requests' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium">Medicine Requests</button>
                <button @click="tab='timeline'" :class="tab === 'timeline' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-1 border-b-2 text-sm font-medium">Timeline</button>
            </nav>
        </div>

        <!-- Overview -->
        <div x-show="tab==='overview'" class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Patient Info</h3>
                    <dl class="divide-y divide-gray-200 text-sm">
                        <div class="py-2 flex justify-between"><dt class="text-gray-500">Name</dt><dd class="text-gray-900">{{ $resident->full_name ?? '-' }}</dd></div>
                        <div class="py-2 flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-900">{{ $resident->email ?? '-' }}</dd></div>
                        <div class="py-2 flex justify-between"><dt class="text-gray-500">Contact</dt><dd class="text-gray-900">{{ $resident->contact_number ?? '-' }}</dd></div>
                        <div class="py-2 flex justify-between"><dt class="text-gray-500">Purok</dt><dd class="text-gray-900">{{ $displayPurok ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Recent Activity</h3>
                    <ul class="divide-y divide-gray-200 text-sm">
                        @forelse($timeline->take(5) as $item)
                            <li class="py-2 flex items-start space-x-3">
                                <span class="mt-0.5">
                                    @if($item['type'] === 'consultation') <i class="fas fa-stethoscope text-green-600"></i>
                                    @else <i class="fas fa-pills text-amber-600"></i>
                                    @endif
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item['title'] }}</p>
                                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</p>
                                    <p class="text-gray-500">{{ $item['details'] }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500">No recent activity yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Consultations -->
        <div x-show="tab==='consultations'" class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-gray-500">Type</th>
                            <th class="px-4 py-2 text-left text-gray-500">Complaint</th>
                            <th class="px-4 py-2 text-left text-gray-500">Attending</th>
                            <th class="px-4 py-2 text-left text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($medicalRecords as $record)
                            <tr>
                                <td class="px-4 py-2 text-gray-900">{{ optional($record->consultation_datetime)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $record->consultation_type ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ Str::limit($record->complaint, 60) ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ optional($record->attendingHealthWorker)->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.medical-records.show', $record->id) }}" class="text-green-700 hover:text-green-900 font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">No consultations yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medicine Requests -->
        <div x-show="tab==='requests'" class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-gray-500">Medicine</th>
                            <th class="px-4 py-2 text-left text-gray-500">Quantity</th>
                            <th class="px-4 py-2 text-left text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-gray-500">Approved By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($medicineRequests as $request)
                            <tr>
                                <td class="px-4 py-2 text-gray-900">{{ optional($request->request_date ?? $request->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ optional($request->medicine)->name ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $request->quantity_requested ?? $request->quantity_approved ?? $request->quantity ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-700 capitalize">{{ $request->status ?? 'pending' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ optional($request->approvedByUser)->full_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">No medicine requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Timeline -->
        <div x-show="tab==='timeline'" class="p-4">
            <div class="space-y-3">
                @forelse($timeline as $item)
                    <div class="flex items-start space-x-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="mt-1">
                            @if($item['type'] === 'consultation') <i class="fas fa-stethoscope text-green-600"></i>
                            @else <i class="fas fa-pills text-amber-600"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center">
                                <p class="font-semibold text-gray-900">{{ $item['title'] }}</p>
                                <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $item['details'] }}</p>
                            @if(!empty($item['link']))
                                <a href="{{ $item['link'] }}" class="text-sm text-green-700 font-medium hover:text-green-900">View record</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No activity yet for this patient.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- Patient Info Modal -->
<div id="patientInfoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Patient Information</h3>
            <button type="button" id="closePatientInfoModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 space-y-3 text-sm text-gray-800">
            <div class="flex justify-between"><span class="text-gray-600">Name</span><span class="font-medium">{{ $resident->full_name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Email</span><span class="font-medium">{{ $resident->email ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Contact</span><span class="font-medium">{{ $resident->contact_number ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Purok</span><span class="font-medium">{{ $displayPurok ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Consultations</span><span class="font-medium">{{ $stats['total_consultations'] ?? 0 }}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Medicine Requests</span><span class="font-medium">{{ $stats['total_requests'] ?? 0 }}</span></div>
        </div>
        <div class="flex justify-end space-x-3 px-4 py-3 border-t border-gray-200">
            <button type="button" id="dismissPatientInfoModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('patientInfoModal');
    const openBtn = document.getElementById('openPatientInfoModal');
    const closeBtn = document.getElementById('closePatientInfoModal');
    const dismissBtn = document.getElementById('dismissPatientInfoModal');

    const showModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const hideModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    openBtn?.addEventListener('click', showModal);
    closeBtn?.addEventListener('click', hideModal);
    dismissBtn?.addEventListener('click', hideModal);
});
</script>
@endpush
