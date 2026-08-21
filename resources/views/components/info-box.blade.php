<april:card class="{{$class}} {{$colour}} {{$textColour}} border-transparent">
    <slot:title class="flex items-center justify-between gap-4 text-3xl md:text-4xl">
        <span>{{$title}}</span>
        <i class="{{$icon}} hidden text-3xl opacity-80 md:block" aria-hidden="true"></i>
    </slot:title>
    <slot:description class="text-current/80">{{$text}}</slot:description>
    @isset ($url)
        <slot:footer class="-mx-6 -mb-6 mt-6 border-t border-white/20 bg-black/10 p-0">
            <a href="{{$url}}" class="flex w-full items-center justify-between px-4 py-3 text-sm font-medium transition-colors hover:bg-black/10 md:px-5">{{$urlText ?? 'View'}} <i class="fa fa-arrow-right text-xs" aria-hidden="true"></i></a>
        </slot:footer>
    @endif
</april:card>
