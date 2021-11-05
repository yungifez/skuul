<ol class="breadcrumb">
    @if (isset($paths))
        @foreach ($paths as $path)
            @if (!in_array('active', $path))
                <li class="breadcrumb-item"><a href="{{$path['href']}}">{{__($path['text'])}}</a></li>
            @else
                <li class="breadcrumb-item active">{{__($path['text'])}}</li>
            @endif
         @endforeach
    @endif
</ol>
