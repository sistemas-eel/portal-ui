@props([
    'title' => '',
    'icon' => null,
    'maxWidth' => '2xl',
    'id' => null,
    'show' => true,
    'closeLabel' => 'Fechar',
    'headerClass' => 'bg-portal-gradient text-white',
])

@php
    $attributeBag = $attributes->getAttributes();
    $wireModelAttribute = collect(array_keys($attributeBag))->first(fn ($key) => strncmp($key, 'wire:model', 10) === 0);
    $wireModel = $wireModelAttribute ? $attributeBag[$wireModelAttribute] : null;
    $isWireModal = ! empty($wireModel);
    $shouldRender = $isWireModal || $show;
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full',
    ];
    $maxWidthClass = isset($widths[$maxWidth]) ? $widths[$maxWidth] : $widths['2xl'];
@endphp

@if($shouldRender)
    <div
        @if($id) id="{{ $id }}" @endif
        {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto']) }}
        data-portal-modal
        @if($isWireModal) data-portal-modal-wire @endif
        role="dialog"
        aria-modal="true"
        @if($title) aria-label="{{ $title }}" @endif
        @if($isWireModal)
            x-data="{ show: $wire.entangle('{{ $wireModel }}') }"
            x-show="show"
            x-cloak
            x-effect="
                if (show) {
                    requestAnimationFrame(() => {
                        $el.querySelector('[data-autofocus]')?.focus()
                    })
                }
            "
        @endif
    >
        <div
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            aria-hidden="true"
            @if($isWireModal)
                @click="show = false"
                @keydown.escape.window="show = false"
            @else
                data-portal-modal-close
            @endif
        ></div>

        <div
            class="relative min-h-full flex items-start justify-center p-0 sm:p-4"
            @if(! $isWireModal) data-portal-modal-surface @endif
            @if($isWireModal)
                @click.self="show = false"
                @keydown.escape.window="show = false"
            @endif
        >
            <div
                class="bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl {{ $maxWidthClass }} w-full mt-20 sm:my-10 relative overflow-hidden"
                @if($isWireModal)
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-10 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-10 sm:scale-95"
                @endif
            >
                <div class="{{ $headerClass }} px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        @if($icon)
                            <i class="fa {{ $icon }}" aria-hidden="true"></i>
                        @endif
                        {{ $title }}
                    </h2>

                    @isset($close)
                        {{ $close }}
                    @else
                        <button
                            type="button"
                            class="cursor-pointer text-white hover:bg-white/20 rounded-lg p-2 transition-all"
                            aria-label="{{ $closeLabel }}"
                            @if($isWireModal)
                                @click="show = false"
                            @else
                                data-portal-modal-close
                            @endif
                        >
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    @endisset
                </div>

                <div class="p-4 sm:p-6">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-2">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endif
