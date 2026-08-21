@props(['colour' => 'bg-red-500', 'title' => null, 'icon' => 'fa fa-ban', 'stackIcons' => [], 'class' => '', 'id' => 'alert', 'timeout' => '5000', 'show' => true])

@php
    $showAlert = filter_var($show, FILTER_VALIDATE_BOOLEAN);
@endphp

<div id="{{$id}}" x-data="{ showAlert: @js($showAlert) }" x-show="showAlert" x-cloak>
    <april:alert
        class="{{$colour}} {{$class}}"
        :timeout="$timeout"
        :dismiss-on-timeout="$attributes->get('dismissOnTimeout') == true"
        dismissable
        {{$attributes}}
    >
        @if ($title !== null)
            <slot:title>{{$title}}</slot:title>
        @endif
        <slot:icon>
            @if (!empty($stackIcons))
                <span class="fa-stack">
                    @foreach ($stackIcons as $stackIcon)
                        <i class="{{$stackIcon}} fa-stack-{{$loop->iteration}}x"></i>
                    @endforeach
                </span>
            @else
                <i class="{{$icon}}" aria-hidden="true"></i>
            @endif
        </slot:icon>
        <slot:description>
            {{$slot}}
        </slot:description>
    </april:alert>
</div>
