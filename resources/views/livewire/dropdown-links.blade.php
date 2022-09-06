<div class="dropdown">
    <x-adminlte-button label="{{$button_label}}" theme="secondary" id="DropDownButton" data-toggle="dropdown" class="text-capitalize"  aria-haspopup="true" aria-expanded="false"/>
    <div class="dropdown-menu" aria-labelledby="DropDownButton">
        @if (isset($links))
            @foreach ($links as $link)
                <a href="{{$link['href']}}" class="dropdown-item text-capitalize">
                    <i class="{{$link['icon']}} pr-2"></i>
                    {{$link['text']}}
                </a>
            @endforeach
        @endif
    </div>
</div>