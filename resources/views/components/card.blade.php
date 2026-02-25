<div {{ $attributes->merge(['class' => 'glass-card p-6 md:p-8']) }}>
    @if(isset($title))
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold">{{ $title }}</h3>
            @if(isset($action))
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="mt-8 pt-6 border-t border-base-200">
            {{ $footer }}
        </div>
    @endif
</div>
