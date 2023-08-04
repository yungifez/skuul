@props(['class' => '', 'width' => 'w-10/12 md:w-8/12 lg:w-5/12 xl:w-4/12', 'height' => 'min-h-[20%]'])
<div class="flex flex-cols justify-center items-center my-12 flex-col">
    <img src="{{@asset(config('app.logo'))}}" alt="" class="rounded-full w-28 h-28 border border-gray-200 shadow-lg my-4">
    <div class="{{$class}} {{$width}} {{$height}}">
        <div class="p-3 w-full flex flex-col justify-center items-center bg-white border border-gray-300 rounded">
            {{$slot}}
        </div>
        <div class="my-3">
            {{$footer ?? null}}
        </div>
    </div>
</div>