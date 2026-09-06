{{-- April UI 1.2.6 carries this fix. Drop this file once the package
     is updated. Overrides april-ui so a card title does not skip a heading level.
     The package fixes the title at h3, so a card sitting under the page
     heading read h1 then h3. Set level to place a card inside a section. --}}
@props(['level' => 2])

@php
$rootAttributes = $attributes->whereDoesntStartWith('header');
$titleLevel = in_array((int) $level, [2, 3, 4, 5, 6], true) ? (int) $level : 2;
@endphp

<div data-slot="card" {{$rootAttributes->twMerge(["rounded-lg border bg-card text-card-foreground shadow-sm"])}}>
    <div data-slot="card-header" class="{{$attributes->get('header-class')}} flex flex-col space-y-1.5 p-6" {{$attributes->
        whereStartsWith('header')}}>
        @isset($title)
        <h{{ $titleLevel }} data-slot="card-title" {{$title->attributes->twMerge(["font-semibold text-2xl leading-none tracking-tight"])}}>{{$title}}</h{{ $titleLevel }}>
        @endisset
        @isset($description)
        <p data-slot="card-description" {{$description->attributes->twMerge(["text-sm text-muted-foreground"])}}>{{$description}}</p>
        @endisset
    </div>
    @isset($content)
    <div data-slot="card-content" {{$content->attributes->twMerge(["p-6 pt-0"])}}>
        {{$content}}
    </div>
    @endisset
    @isset($footer)
    <div data-slot="card-footer" {{$footer->attributes->twMerge(["flex items-center p-6 pt-0"])}} >
        {{$footer}}
    </div>
    @endisset
</div>
