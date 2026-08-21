@props(['buttonLabel' => 'Actions', 'links' => [], 'groupClass' => null, 'buttonClass' => null, 'dropdownClass' => null])

<april:dropdown-menu class="{{$groupClass}}">
    <slot:trigger>
        <april:button variant="outline" size="sm" class="{{$buttonClass}}" type="button" aria-haspopup="true">
            {{$buttonLabel}}
            <i class="fas fa-angle-down text-xs" aria-hidden="true"></i>
        </april:button>
    </slot:trigger>
    <slot:content class="{{$dropdownClass}} min-w-40 p-1">
        {{$slot}}
    </slot:content>
</april:dropdown-menu>
