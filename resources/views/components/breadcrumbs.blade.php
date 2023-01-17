@props(['paths' => []])

<ol class="flex gap-2 items-center">
    @isset ($paths)
        <i class="fa fa-home" aria-hidden="true"></i>
        @foreach ($paths as $path)
            @if (!in_array('active', $path))
                <li class="breadcrumb-item text-capitalize"><a class="text-blue-600" href="{{$path['href']}}">{{__($path['text'])}}</a></li>
            @else
                <li class="breadcrumb-item active text-capitalize">{{__($path['text'])}}</li>
            @endif
            @if (!$loop->last)
                <i class="fa fa-angle-right" aria-hidden="true"></i>
            @endif
         @endforeach
    @endif
</ol>