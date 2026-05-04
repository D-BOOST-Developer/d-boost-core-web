@php
    $isStorefront = ($sale->order_source ?? 'walk_in') === 'storefront';
    $sourceClasses = $isStorefront
        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
        : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
    $sourceLabel = $isStorefront ? 'Storefront' : 'Walk-in';
@endphp
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $sourceClasses }}">
    {{ $sourceLabel }}
</span>
