@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-secondary text-start text-base font-semibold text-brand-secondary bg-black/10 focus:outline-none focus:text-white focus:bg-black/20 focus:border-brand-secondary transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/85 hover:text-white hover:bg-black/5 hover:border-white/20 focus:outline-none focus:text-white focus:bg-black/5 focus:border-white/20 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

