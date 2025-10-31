{{-- Resident FAQ Skeleton Component --}}
<div class="animate-pulse space-y-6" data-skeleton>
	<!-- Header and Search -->
	<div class="text-center">
		<div class="h-8 w-72 bg-gray-200 rounded mb-3 mx-auto"></div>
		<div class="h-5 w-96 bg-gray-100 rounded mb-4 mx-auto"></div>
		<div class="flex flex-wrap justify-center gap-3 mb-4">
			<div class="h-10 w-[32rem] max-w-[90vw] bg-gray-200 rounded"></div>
			<div class="h-10 w-28 bg-gray-200 rounded"></div>
		</div>
		<!-- Category Tabs -->
		<div class="flex flex-wrap justify-center gap-2">
			@for ($i = 0; $i < 6; $i++)
			<div class="h-8 w-24 bg-gray-200 rounded"></div>
			@endfor
		</div>
	</div>

	<!-- FAQ Accordion List -->
	<div class="space-y-3">
		@for ($i = 0; $i < 8; $i++)
		<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4">
			<div class="flex items-start justify-between">
				<div class="flex-1">
					<div class="h-5 w-[40rem] max-w-[85vw] bg-gray-200 rounded mb-2"></div>
					<div class="h-4 w-[36rem] max-w-[80vw] bg-gray-100 rounded"></div>
				</div>
				<div class="w-8 h-8 bg-gray-200 rounded-full ml-3"></div>
			</div>
		</div>
		@endfor
	</div>

	<!-- Suggested Articles / Help -->
	<div class="bg-white rounded-xl shadow-lg border border-gray-100">
		<div class="p-5">
			<div class="h-6 w-40 bg-gray-200 rounded mb-3"></div>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				@for ($i = 0; $i < 4; $i++)
				<div class="border border-gray-200 rounded-lg p-4">
					<div class="h-5 w-48 bg-gray-200 rounded mb-2"></div>
					<div class="space-y-2">
						<div class="h-4 w-60 bg-gray-200 rounded"></div>
						<div class="h-4 w-52 bg-gray-200 rounded"></div>
					</div>
				</div>
				@endfor
			</div>
		</div>
	</div>
</div>


