@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#111118] border-[#2A2A3A] text-[#F1F0FF] placeholder-[#5A5A7A] focus:border-[#A78BFA] focus:ring-[#A78BFA] rounded-lg shadow-sm transition-colors']) }}>
