<april:card class="{{$class}} {{$colour}} {{$textColour}} border-transparent">
    <slot:title class="flex items-center justify-between gap-4 text-3xl md:text-4xl">
        <span>{{$title}}</span>
        @if (filled($icon))
            <x-icon :name="'lucide-'.$icon" class="hidden size-8 opacity-80 md:block" />
        @endif
    </slot:title>
    <slot:description class="text-current/80">{{$text}}</slot:description>
    @isset ($url)
    <slot:footer class="-mx-6 -mb-6 mt-6 border-t border-white/20 bg-black/10 p-0">
        <a href="{{$url}}"
            class="flex w-full items-center justify-between px-4 py-3 text-sm font-medium transition-colors hover:bg-black/10 md:px-5">{{$urlText
            ?? 'View'}} <x-lucide-arrow-right class="size-4"  /></a>
    </slot:footer>
    @endisset
</april:card>
