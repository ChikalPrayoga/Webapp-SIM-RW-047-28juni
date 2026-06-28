@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate-200 hover:border-slate-300 focus:border-brand-primary focus:ring focus:ring-brand-primary focus:ring-opacity-20 rounded-xl shadow-sm transition duration-150']) !!}>

