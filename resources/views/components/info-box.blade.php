<div class="{{$class}} {{$colour}} {{$textColour}} shadow rounded">
    <div class="p-2 md:p-3 text-center md:text-left md:flex gap-4 items-center justify-between border-b">
        <div>
            <h3 class="text-4xl md:text-5xl my-3 font-bold">{{$title}}</h3>
            <p class="text-xl my-3">{{$text}}</p>
        </div>
        <i class="{{$icon}} m-4 text-center text-7xl hidden md:block" aria-hidden="true"></i>
    </div>
    @isset ($url)
        <div class="w-full bg-black bg-opacity-30 flex items-center justify-center">
            <a href="{{$url}}" class="w-full py-2 md:py-3 text-center">{{$urlText ?? 'View'}} <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
        </div>
    @endif
</div>