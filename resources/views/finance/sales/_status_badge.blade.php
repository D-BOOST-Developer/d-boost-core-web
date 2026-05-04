@php
    $statusColors = [
        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'refunded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    ];
    $statusColor = $statusColors[$sale->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';

    $statusLabel = ucfirst($sale->status);
    if ($sale->status === 'completed' && $sale->order_source === 'storefront') {
        $statusLabel = 'Confirmed';
    }
@endphp
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
    {{ $statusLabel }}
</span>
