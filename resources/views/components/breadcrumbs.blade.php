@props(['paths' => []])

<ol class="capitalize my-4">
    @isset ($paths)
        <i class="fa fa-home mr-2" aria-hidden="true"></i>
        @foreach ($paths as $path)
            @if (!in_array('active', $path))
                <li class="breadcrumb-item inline mx-1 text-capitalize text-sm"><a class="text-blue-600 dark:text-blue-400" href="{{$path['href']}}">{{__($path['text'])}}</a></li>
            @else
                <li class="breadcrumb-item inline mx-1 active text-capitalize text-sm">{{__($path['text'])}}</li>
            @endif
            @if (!$loop->last)
                <i class="fa fa-angle-right inline mx-1 text-sm" aria-hidden="true"></i>
            @endif
         @endforeach
    @endif
</ol>