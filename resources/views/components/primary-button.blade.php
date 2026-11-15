<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#A78BFA] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#9333EA] focus:bg-[#9333EA] active:bg-[#7C3AED] focus:outline-none focus:ring-2 focus:ring-[#A78BFA] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
