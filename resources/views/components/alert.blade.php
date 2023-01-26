@props(['colour' => 'bg-red-500', 'title', 'icon' => 'fa fa-ban', 'class' => '', 'id' => 'alert'])

<div @class(["$colour $class p-3 text-white rounded w-full"]) aria-role="alert" x-data="{'showAlert' : true}" x-show="showAlert" x-transition id="{{$id}}">
    <div class="flex gap-3 justify-between">
        <div class="flex gap-3 items-center">
            <i class="text-xl {{$icon}}"></i>
            <p class="text-xl">
                {{$title}}
            </p>
        </div>
        <div>
            <i class="fas fa-x text-lg mx-2 cursor-pointer" aria-role="button" aria-hidden="true" @click="showAlert = false">
                <p class="sr-only">Close Alert</p>
            </i>
        </div>
    </div>
    <div class="p-3">
        {{$slot}}
    </div>
</div>