@props(['buttonLabel' => 'Actions', 'links'])

<div class="relative flex flex-col items-center justify-between" x-data="{dropdown : false}" >
    <x-button label="{{$buttonLabel}}" class="bg-gray-500 capitalize border"  aria-haspopup="true" aria-expanded="false" @click="dropdown = !dropdown"/>
    <div class="absolute top-14 p-2 w-36 z-30 bg-white border dark:bg-gray-800 rounded" x-show="dropdown">
        {{$slot}}
    </div>
</div>