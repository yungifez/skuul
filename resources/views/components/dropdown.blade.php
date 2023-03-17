@props(['buttonLabel' => 'Actions', 'links', 'groupClass' => null,  'buttonClass' => null, 'dropdownClass' => null])

<div class="{{$groupClass}} relative flex flex-col items-center justify-between" x-data="{dropdown : false}" >
    <x-button label="{{$buttonLabel}}" icon="fas fa-angle-down" class="{{$buttonClass}} bg-gray-500 capitalize border"  aria-haspopup="true" type="button" aria-expanded="false" @click="dropdown = !dropdown"/>
    <div @click.outside="dropdown = false" class="{{$dropdownClass}} absolute top-14 p-2 min-w-[36] z-30 bg-white border dark:bg-gray-800 rounded" x-show="dropdown" style="display: none">
        {{$slot}}
    </div>
</div>