{{-- Usage: @include('components.loading-wrapper', ['loading' => $loading, 'skeleton' => 'components.loading.skeleton-table']) --}}
@if (!empty($loading) && $loading)
    @include($skeleton)
@else
    {{ $slot ?? '' }}
@endif 