<april:button class="{{$class}} {{$colour}}" {{$attributes}}>
    @if ($icon)
        <i class="{{$icon}}" aria-hidden="true"></i>
    @endif
    {{$slot}} {{$label}}
</april:button>
