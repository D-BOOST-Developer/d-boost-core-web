@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-gray-300 text-gray-900 placeholder-gray-400 focus:border-[#006BFF] focus:ring-[#006BFF] rounded-lg shadow-sm transition-colors']) }}>
