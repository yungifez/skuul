<div class="dropdown">
    <button class="btn" type="button" id="DropDownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <p class="text-capitalize text-black">{{$button_label}}</p>
    </button>
    <div class="dropdown-menu" aria-labelledby="DropDownButton">
        @if (isset($links))
            @foreach ($links as $link)
                <a href="{{$link['href']}}" class="dropdown-item">
                    <i class="{{$link['icon']}} pr-2"></i>
                    {{$link['text']}}
                </a>
            @endforeach
        @endif
    </div>
</div>