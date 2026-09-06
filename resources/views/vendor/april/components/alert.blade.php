{{-- April UI 1.2.6 carries this fix. Drop this file once the package
     is updated. Overrides april-ui so an alert title does not skip a heading level.
     The package fixes the title at h5, so an alert under the page heading
     read h1 then h5. Set level to place an alert inside a section. --}}
@props([
'level' => 2,
'title',
'icon',
'class' => '',
'timeout' => '5000',
'dismissable' => false,
'dismissOnTimeout' => false,
'startTimeoutOnIntersect' => false
])

@php
$titleLevel = in_array((int) $level, [2, 3, 4, 5, 6], true) ? (int) $level : 2;

$class = $class." "."relative w-full rounded-lg border p-4 flex gap-x-3 ";

// Wrapper components may provide text props instead of ComponentSlot instances.
// Keep slot attributes available when a real named slot is provided, while
// allowing plain strings to be used for simple titles and descriptions.
$slotAttributes = static fn ($slot) => $slot instanceof \Illuminate\View\ComponentSlot
    ? $slot->attributes
    : new \Illuminate\View\ComponentAttributeBag;

$class .= match($attributes->get("variant")){
default => "bg-background text-foreground fill-foreground",
'destructive' => "border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive
fill-destructive",
"none" => "",
};
@endphp

<div data-slot="alert" role="alert" data-state="open" x-data='alert(@json($dismissOnTimeout),{{$timeout}},@json($startTimeoutOnIntersect))' x-bind="root"
    {{$attributes->
    class(["$class"])}}
    >
    @isset($icon)
    <div {{$slotAttributes($icon)->twMerge(["flex items-start mt-2"])}}>
        {{$icon}}
    </div>
    @endisset

    <div class="w-full">
        @isset($title)
        <h{{ $titleLevel }} data-slot="alert-title" {{$slotAttributes($title)->twMerge(["mb-1 font-medium leading-none tracking-tight"])}}>
            {{$title}}
        </h{{ $titleLevel }}>
        @endisset
        @isset($description)
        <div data-slot="alert-description" {{$slotAttributes($description)->twMerge(["text-sm"])}}>
            {{$description}}
        </div>
        @endisset
    </div>
    <div>
        @if ($dismissable == true)
        <div x-bind="dismissTrigger">
            @isset($close)
            {{$close}}
            @else
            <button type="button"
                class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none ">
                <april:x />
                <span class="sr-only">Close</span>
            </button>
            @endisset
        </div>
        @endif
    </div>
</div>
