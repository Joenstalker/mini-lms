@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input input-bordered w-full rounded-xl bg-base-100 border-base-300 focus:border-primary focus:ring-primary/20 transition-all duration-200']) }}>
