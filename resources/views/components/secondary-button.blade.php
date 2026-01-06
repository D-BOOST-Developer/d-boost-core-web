<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg font-semibold text-xs text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-[#00FF9C] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
