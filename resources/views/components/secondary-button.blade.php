<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline border-base-300 rounded-xl px-6 hover:bg-base-200 hover:border-base-300 transition-all duration-200']) }}>
    {{ $slot }}
</button>
