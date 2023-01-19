@props(['colour' => 'bg-red-500', 'title', 'icon' => '', 'class' => ''])

<div @class(["$colour $class p-3 text-white rounded w-full"])>
    <div class="flex gap-3 justify-between">
        <div class="flex gap-3 items-center">
            <i class="text-xl {{$icon}}"></i>
            <p class="text-xl">
                {{$title}}
            </p>
        </div>
        <div>
            <i class="fas fa-x text-lg mx-2" aria-hidden="true"></i>
        </div>
    </div>
    <div class="p-3">
        {{$slot}}
    </div>
</div>