@props(['colour' => 'bg-red-500', 'title', 'icon' => 'fa fa-ban','stackIcons' => [] , 'class' => '', 'id' => 'alert', 'timeout' => '5000', 'show' => true])

<div @class(["$colour $class p-3 text-white rounded w-full"]) aria-role="alert" x-data="{'showAlert' : {{$show}}}" x-show="showAlert" x-transition id="{{$id}}" {{$attributes}} style="display:none">
    <div class="flex gap-3 justify-between">
        <div class="flex gap-3 items-center">
            @if (!empty($stackIcons))
                <span class="fa-stack">
                    @foreach ($stackIcons as $stackIcon)
                        <i class="{{$stackIcon}} fa-stack-{{$loop->iteration}}x"></i>
                    @endforeach
                </span>
            @else
                <i class="text-xl {{$icon}}"></i>
            @endif
            <p class="text-xl">
                {{$title}}
            </p>
        </div>
        @if ($attributes->get('dismissOnTimeout') == true)
            <span x-init="setTimeout(() => { showAlert = false }, {{$timeout}});"></span>
        @endif
        
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