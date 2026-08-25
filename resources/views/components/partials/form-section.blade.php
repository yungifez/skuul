@props(['submit'])

<div {{ $attributes->merge(['class' => 'grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] lg:items-start']) }}>
    <div class="flex flex-col gap-2 lg:pt-2">
        <h2 class="text-lg font-semibold tracking-tight">{{$title}}</h2>
        <p class="text-sm leading-6 text-muted-foreground">{{$description}}</p>
    </div>

    <form wire:submit="{{ $submit }}">
        <april:card>
            <slot:content>
                <div class="flex flex-col gap-6 pt-6">
                    {{ $form }}
                </div>
            </slot:content>

            @if (isset($actions))
                <slot:footer class="justify-end gap-3">
                    {{ $actions }}
                </slot:footer>
            @endif
        </april:card>
    </form>
</div>
