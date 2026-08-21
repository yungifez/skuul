@props(['paths' => []])

<ol class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" data-slot="breadcrumb">
    @isset ($paths)
        <x-lucide-house class="size-3.5"  />
        @foreach ($paths as $path)
            @if (!in_array('active', $path))
                <li class="breadcrumb-item inline"><a class="transition-colors hover:text-foreground" href="{{$path['href']}}">{{__($path['text'])}}</a></li>
            @else
                <li class="breadcrumb-item inline font-medium text-foreground">{{__($path['text'])}}</li>
            @endif
            @if (!$loop->last)
                <x-lucide-chevron-right class="inline size-3.5"  />
            @endif
         @endforeach
    @endif
</ol>
