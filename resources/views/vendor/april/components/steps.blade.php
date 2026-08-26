@props([
    'items' => [],
    'current' => 1,
    'orientation' => 'horizontal',
])

@php
    $orientation = in_array($orientation, ['horizontal', 'vertical'], true) ? $orientation : 'horizontal';
    $currentStep = $current ?? 1;
    $itemCount = count($items);
@endphp

<nav
    data-slot="steps"
    data-orientation="{{ $orientation }}"
    aria-label="Progress"
    {{ $attributes->twMerge(['w-full']) }}
>
    <ol @class([
        'flex w-full',
        'flex-col gap-8 sm:flex-row sm:items-start sm:gap-0' => $orientation === 'horizontal',
        'mx-auto max-w-md flex-col gap-8' => $orientation === 'vertical',
    ])>
        @foreach ($items as $index => $item)
            @php
                $step = $item['step'] ?? $item['value'] ?? ($index + 1);
                $explicitState = strtolower((string) ($item['state'] ?? ''));
                $isCurrent = (string) $step === (string) $currentStep;

                if (in_array($explicitState, ['complete', 'completed'], true)) {
                    $state = 'completed';
                } elseif (in_array($explicitState, ['current', 'active'], true)) {
                    $state = 'active';
                } elseif (in_array($explicitState, ['upcoming', 'inactive'], true)) {
                    $state = 'inactive';
                } elseif ($isCurrent) {
                    $state = 'active';
                } elseif (is_numeric($step) && is_numeric($currentStep) && (float) $step < (float) $currentStep) {
                    $state = 'completed';
                } else {
                    $state = 'inactive';
                }

                $isLast = $index === $itemCount - 1;
                $href = $item['href'] ?? null;
                $label = $item['label'] ?? 'Step '.($index + 1);
                $description = $item['description'] ?? null;
                $titleId = 'step-title-'.$index;
                $descriptionId = 'step-description-'.$index;
                $isLinked = filled($href) && $state === 'completed';
            @endphp

            <li
                data-slot="step"
                data-state="{{ $state }}"
                data-step="{{ $step }}"
                aria-posinset="{{ $index + 1 }}"
                aria-setsize="{{ $itemCount }}"
                @class([
                    'group relative min-w-0',
                    'flex flex-row items-start gap-4 sm:flex-1 sm:flex-col sm:items-center sm:gap-0' => $orientation === 'horizontal',
                    'flex items-start gap-4' => $orientation === 'vertical',
                ])
            >
                @if ($orientation === 'horizontal' && !$isLast)
                    <span
                        data-slot="step-separator"
                        aria-hidden="true"
                        @class([
                            'absolute z-0 hidden h-0.5 bg-border sm:block',
                            'left-[calc(50%+1.125rem)] right-[calc(-50%+1.125rem)] top-4',
                            'group-data-[state=completed]:bg-primary',
                        ])
                    ></span>
                @elseif ($orientation === 'vertical' && !$isLast)
                    <span
                        data-slot="step-separator"
                        aria-hidden="true"
                        @class([
                            'absolute bottom-0 left-4 top-9 z-0 w-0.5 bg-border',
                            'h-[calc(100%+2rem)]',
                            'group-data-[state=completed]:bg-primary',
                        ])
                    ></span>
                @endif

                @if ($isLinked)
                    <a
                        href="{{ $href }}"
                        data-slot="step-trigger"
                        aria-labelledby="{{ $titleId }}"
                        @if (filled($description)) aria-describedby="{{ $descriptionId }}" @endif
                        aria-label="{{ $label }} completed"
                        class="group/trigger relative z-10 flex shrink-0 items-center gap-3 sm:flex-col"
                    >
                @else
                    <button
                        type="button"
                        data-slot="step-trigger"
                        aria-labelledby="{{ $titleId }}"
                        @if (filled($description)) aria-describedby="{{ $descriptionId }}" @endif
                        @if ($state === 'active') aria-current="step" @endif
                        @if ($state !== 'completed' || blank($href)) disabled @endif
                        class="group/trigger relative z-10 flex shrink-0 items-center gap-3 sm:flex-col"
                    >
                @endif
                    <span
                        data-slot="step-indicator"
                        @class([
                            'flex size-9 shrink-0 items-center justify-center rounded-full border bg-background text-sm font-medium transition-colors',
                            'border-primary bg-primary text-primary-foreground' => $state === 'completed',
                            'border-primary bg-primary text-primary-foreground ring-4 ring-primary/15' => $state === 'active',
                            'border-muted-foreground/25 text-muted-foreground' => $state === 'inactive',
                        ])
                    >
                        @if ($state === 'completed')
                            <x-lucide-check class="size-4" aria-hidden="true" />
                            <span class="sr-only">Completed</span>
                        @elseif ($state === 'active')
                            <span class="size-2 rounded-full bg-current" aria-hidden="true"></span>
                            <span class="sr-only">Current step</span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>

                    <span @class([
                        'min-w-0 text-left sm:text-center',
                        'flex flex-col gap-1' => $orientation === 'vertical',
                    ])>
                        <span
                            id="{{ $titleId }}"
                            data-slot="step-title"
                            @class([
                                'block truncate text-sm font-medium',
                                'text-foreground' => $state !== 'inactive',
                                'text-muted-foreground' => $state === 'inactive',
                            ])
                        >{{ $label }}</span>

                        @if (filled($description))
                            <span
                                id="{{ $descriptionId }}"
                                data-slot="step-description"
                                class="text-xs text-muted-foreground {{ $orientation === 'horizontal' ? 'hidden sm:block' : '' }}"
                            >{{ $description }}</span>
                        @endif
                    </span>
                @if ($isLinked)
                    </a>
                @else
                    </button>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
