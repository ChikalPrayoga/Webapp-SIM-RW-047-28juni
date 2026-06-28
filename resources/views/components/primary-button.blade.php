<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-brand-primary border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#003B31] focus:bg-[#003B31] active:bg-[#002B24] focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

