@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-[#006BFF]']) }}>
        {{ $status }}
    </div>
@endif
