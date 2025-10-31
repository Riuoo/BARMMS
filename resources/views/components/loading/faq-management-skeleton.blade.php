{{-- FAQ Management Skeleton Component (matches live layout) --}}
<div class="animate-pulse space-y-4" data-skeleton>
	<!-- Header: Title + Add FAQ button -->
	<div class="flex items-center justify-between mb-2">
		<div class="h-8 w-64 bg-gray-200 rounded"></div>
		<div class="h-10 w-36 bg-gray-200 rounded"></div>
	</div>

	<!-- Filters: search input + category select + filter button -->
	<div class="flex flex-wrap gap-2 items-center mb-2">
		<div class="h-10 w-[20rem] max-w-[80vw] bg-gray-200 rounded"></div>
		<div class="h-10 w-48 bg-gray-200 rounded"></div>
		<div class="h-10 w-24 bg-gray-200 rounded"></div>
	</div>

	<!-- Table container -->
	<div class="bg-white rounded-lg shadow-sm border border-gray-200">
		<div class="p-4">
			<!-- Table header columns: drag, order, question, category, status, edit, delete -->
			<div class="grid grid-cols-12 gap-3 items-center mb-2">
				<div class="h-5 w-6 bg-gray-100 rounded col-span-1"></div>
				<div class="h-5 w-16 bg-gray-100 rounded col-span-1"></div>
				<div class="h-5 w-40 bg-gray-100 rounded col-span-5"></div>
				<div class="h-5 w-28 bg-gray-100 rounded col-span-2"></div>
				<div class="h-5 w-20 bg-gray-100 rounded col-span-1"></div>
				<div class="h-5 w-12 bg-gray-100 rounded col-span-1"></div>
				<div class="h-5 w-12 bg-gray-100 rounded col-span-1"></div>
			</div>
			<!-- Table rows -->
			@for ($i = 0; $i < 7; $i++)
			<div class="grid grid-cols-12 gap-3 items-center py-3 border-t border-gray-100 first:border-t-0">
				<div class="h-4 w-5 bg-gray-200 rounded col-span-1"></div>
				<div class="h-4 w-10 bg-gray-200 rounded col-span-1"></div>
				<div class="h-4 w-full bg-gray-200 rounded col-span-5"></div>
				<div class="h-4 w-24 bg-gray-200 rounded col-span-2"></div>
				<div class="h-6 w-16 bg-gray-200 rounded-full col-span-1"></div>
				<div class="h-4 w-10 bg-gray-200 rounded col-span-1"></div>
				<div class="h-4 w-10 bg-gray-200 rounded col-span-1"></div>
			</div>
			@endfor
		</div>
	</div>

	<!-- Save Order button -->
	<div class="mt-2 flex justify-start">
		<div class="h-10 w-32 bg-gray-200 rounded"></div>
	</div>
</div>


