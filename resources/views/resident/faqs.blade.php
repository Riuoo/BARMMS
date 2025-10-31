@extends('resident.layout')

@section('title', 'FAQ / Quick Help')

@section('content')
<!-- Resident FAQ Skeleton -->
<div id="residentFaqSkeleton" class="max-w-4xl mx-auto pt-2">
    @include('components.loading.resident-faq-skeleton')
    </div>

<!-- Real Content (auto-shown by layout when skeleton hidden) -->
<div id="residentFaqContent" class="max-w-4xl mx-auto pt-2">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">FAQ & Quick Help</h1>
            <p class="text-gray-600 text-base max-w-xl">Find answers to common questions, step-by-step help, and important hotline contacts.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('resident.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"><i class="fas fa-home mr-2"></i>Dashboard</a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('resident.faqs') }}" class="mb-4 flex flex-col md:flex-row md:items-center gap-2">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('resident.faqs') }}" class="px-3 py-2 rounded text-sm font-medium {{ !request('category') ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-800' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('resident.faqs',['category'=>$cat]) }}" class="px-3 py-2 rounded text-sm font-medium {{ request('category') === $cat ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-800' }}">{{ $cat }}</a>
            @endforeach
        </div>
        <div class="flex-1">
            <input type="text" name="search" placeholder="Search FAQ or keywords..." class="mt-2 md:mt-0 block w-full px-3 py-2 border border-gray-300 rounded focus:ring-green-500 focus:border-green-500" value="{{ request('search') }}">
        </div>
    </form>

    @if($faqs->count()==0)
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question-circle text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No FAQs found</h3>
            <p class="text-gray-600 mb-4">Nothing matched your search or filters. Try another keyword or check another category.</p>
            <a href="{{ route('resident.faqs') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Clear Filters</a>
        </div>
    @else
        @php
        $grouped = $faqs->groupBy('category');
        @endphp
        <div class="space-y-10">
            @foreach($grouped as $cat=>$catFaqs)
                <section>
                    <div class="mb-2 flex items-center gap-2">
                        <h2 class="text-xl font-bold text-gray-900">{{ $cat }}</h2>
                        @if(Str::contains(strtolower($cat), 'hotline'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700"><i class="fas fa-phone-alt mr-1"></i>Important Contacts</span>
                        @endif
                    </div>
                    <div class="bg-white rounded-lg border border-gray-200 divide-y">
                        @foreach($catFaqs as $faq)
                            <div class="p-4">
                                <button type="button" class="w-full flex items-center justify-between text-left text-base font-semibold text-green-900 focus:outline-none" onclick="this.nextElementSibling.classList.toggle('hidden')">
                                    <span><i class="fas fa-question-circle mr-2 text-green-400"></i>{{ $faq->question }}</span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>
                                <div class="pt-3 text-gray-700 px-2 transition hidden">{!! $faq->answer !!}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	setTimeout(function(){
		var skeleton = document.getElementById('residentFaqSkeleton');
		if (skeleton) skeleton.style.display = 'none';
		var content = document.getElementById('residentFaqContent');
		if (content) content.style.display = '';
	}, 400);
});
</script>
@endpush

