<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary rounded-xl px-6 shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200']) }}>
    {{ $slot }}
</button>
