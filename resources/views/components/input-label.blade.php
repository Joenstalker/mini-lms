@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-[10px] uppercase tracking-widest text-white/40 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
