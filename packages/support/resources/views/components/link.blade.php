@props([
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'icon' => null,
    'iconPosition' => 'before',
    'iconSize' => null,
    'indicator' => null,
    'indicatorColor' => 'primary',
    'keyBindings' => null,
    'size' => 'md',
    'tag' => 'a',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $iconSize ??= $size;

    $linkClasses = [
        'filament-link relative inline-flex items-center justify-center gap-0.5 font-medium outline-none hover:underline focus:underline disabled:pointer-events-none disabled:opacity-70',
        'pe-4' => $indicator,
        'pointer-events-none opacity-70' => $disabled,
        match ($color) {
            'gray' => 'text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200',
            default => 'text-custom-600 hover:text-custom-500 dark:text-custom-500 dark:hover:text-custom-400',
        },
        match ($size) {
            'sm' => 'text-sm',
            'md' => 'text-sm',
            'lg' => 'text-base',
        },
    ];

    $cssVariables = Filament\Support\get_color_css_variables($color, shades: [400, 500, 600], except: ['gray']);

    $iconSize = match ($iconSize) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $iconSize,
    };

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-link-icon',
        'me-1' => $iconPosition === 'before',
        'ms-1' => $iconPosition === 'after',
    ]);

    $indicatorClasses = \Illuminate\Support\Arr::toCssClasses([
        'filament-link-indicator absolute -top-1 -end-1 inline-flex items-center justify-center h-4 w-4 rounded-full text-[0.5rem] font-medium text-white',
        match ($indicatorColor) {
            'danger' => 'bg-danger-600',
            'gray' => 'bg-gray-600',
            'info' => 'bg-info-600',
            'primary' => 'bg-primary-600',
            'secondary' => 'bg-secondary-600',
            'success' => 'bg-success-600',
            'warning' => 'bg-warning-600',
            default => $indicatorColor,
        },
    ]);

    $wireTarget = $attributes->whereStartsWith(['wire:target', 'wire:click'])->first();

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }
@endphp

@if ($tag === 'a')
    <a
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        style="{{ $cssVariables }}"
        {{ $attributes->class($linkClasses) }}
    >
        @if ($icon && $iconPosition === 'before')
            <x-filament::icon
                :name="$icon"
                group="support::link.prefix"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        {{ $slot }}

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :name="$icon"
                group="support::link.suffix"
                :size="$iconSize"
                :class="$iconClasses"
            />
        @endif

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </a>
@elseif ($tag === 'button')
    <button
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled,
                    'type' => $type,
                ], escape: false)
                ->class($linkClasses)
        }}
    >
        @if ($iconPosition === 'before')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::link.prefix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        {{ $slot }}

        @if ($iconPosition === 'after')
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    group="support::link.suffix"
                    :size="$iconSize"
                    :class="$iconClasses"
                    :wire:loading.remove.delay="$hasLoadingIndicator"
                    :wire:target="$hasLoadingIndicator ? $loadingIndicatorTarget : null"
                />
            @endif

            @if ($hasLoadingIndicator)
                <x-filament::loading-indicator
                    wire:loading.delay=""
                    :wire:target="$loadingIndicatorTarget"
                    :class="$iconClasses . ' ' . $iconSize"
                />
            @endif
        @endif

        @if ($indicator)
            <span class="{{ $indicatorClasses }}">
                {{ $indicator }}
            </span>
        @endif
    </button>
@endif
