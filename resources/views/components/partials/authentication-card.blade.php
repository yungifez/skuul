@props(['class' => '', 'width' => '', 'height' => '', 'title' => null, 'description' => null])

<div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-muted/30 px-4 py-10 sm:px-6">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-accent/30 to-transparent" aria-hidden="true"></div>

    <main class="relative w-full max-w-md {{ $class }} {{ $width }} {{ $height }}">
        <div class="mb-8 flex flex-col items-center gap-3 text-center">
            <img src="{{ asset(config('app.logo')) }}" alt="{{ config('app.name') }} logo" class="h-16 w-16 rounded-2xl border bg-background object-cover shadow-lg">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-muted-foreground">{{ config('app.name') }}</p>
        </div>

        <april:card class="w-full border-border/70 bg-card/95 shadow-xl shadow-black/5 backdrop-blur" header-class="border-0">
            @if ($title)
                <slot:title class="text-2xl tracking-tight">{{ $title }}</slot:title>
            @endif
            @if ($description)
                <slot:description>{{ $description }}</slot:description>
            @endif
            <slot:content class="space-y-6">
                {{ $slot }}
            </slot:content>
        </april:card>

        @if (isset($footer))
            <div class="pt-5 text-center text-sm text-muted-foreground">
                {{ $footer }}
            </div>
        @endif
    </main>
</div>
