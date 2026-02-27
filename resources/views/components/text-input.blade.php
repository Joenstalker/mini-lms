@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input input-bordered w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-white/20 focus:border-white/30 focus:ring-white/5 transition-all duration-200']) }}>
