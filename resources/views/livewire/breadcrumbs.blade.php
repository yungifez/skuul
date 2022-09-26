<ol class="breadcrumb">
    @if (isset($paths))
        @foreach ($paths as $path)
            @if (!in_array('active', $path))
                <li class="breadcrumb-item text-capitalize"><a href="{{$path['href']}}">{{__($path['text'])}}</a></li>
            @else
                <li class="breadcrumb-item active text-capitalize">{{__($path['text'])}}</li>
            @endif
         @endforeach
    @endif
</ol>
