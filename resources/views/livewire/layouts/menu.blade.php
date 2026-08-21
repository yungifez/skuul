@php
    $menuGroups = [];
    $currentGroup = [
        'label' => 'Workspace',
        'can' => null,
        'items' => [],
    ];

    foreach ($menu as $menuItem) {
        if (isset($menuItem['header'])) {
            if ($currentGroup['items'] !== []) {
                $menuGroups[] = $currentGroup;
            }

            $currentGroup = [
                'label' => $menuItem['header'],
                'can' => $menuItem['can'] ?? null,
                'items' => [],
            ];

            continue;
        }

        $currentGroup['items'][] = $menuItem;
    }

    if ($currentGroup['items'] !== []) {
        $menuGroups[] = $currentGroup;
    }
@endphp

{{-- april:sidebar renders a desktop and a mobile root. Livewire needs one root
     element, so wrap them. `contents` keeps the wrapper out of the box tree. --}}
<div class="contents">
<april:sidebar collapsible="icon" class="border-r">
    <slot:header>
        <a href="{{route('home')}}" class="flex h-10 items-center gap-2 px-2 group-data-[collapsible=icon]:justify-center" aria-label="Home">
            <img src="{{asset(current_school()->logoURL ?? config('app.logo'))}}" alt="" class="h-8 w-8 rounded-md border object-cover">
            <span class="truncate text-sm font-semibold group-data-[collapsible=icon]:hidden">{{config('app.name')}}</span>
        </a>
    </slot:header>

    <slot:content>
        @foreach ($menuGroups as $group)
            @if ($group['can'] === null || auth()->user()->can($group['can']))
                <april:sidebar-group>
                    <april:sidebar-group-label>{{$group['label']}}</april:sidebar-group-label>
                    <april:sidebar-group-content>
                        <april:sidebar-menu>
                            @foreach ($group['items'] as $menuItem)
                                @if (!isset($menuItem['can']) || auth()->user()->can($menuItem['can']))
                                    @if (isset($menuItem['submenu']))
                                        @php
                                            $submenuIsOpen = in_array(Route::currentRouteName(), array_column($menuItem['submenu'], 'route'));
                                        @endphp
                                        <div x-data="{ open: @js($submenuIsOpen) }">
                                            <april:sidebar-menu-item>
                                                <april:sidebar-menu-button type="button" x-on:click="open = !open" x-bind:data-state="open ? 'open' : 'closed'">
                                                    <i class="w-4 shrink-0 text-center text-xs {{$menuItem['icon'] ?? 'fa fa-circle'}}" aria-hidden="true"></i>
                                                    <span>{{$menuItem['text']}}</span>
                                                    <i class="ml-auto text-xs group-data-[collapsible=icon]:!hidden" :class="open ? 'fa fa-angle-down' : 'fa fa-angle-right'" aria-hidden="true"></i>
                                                </april:sidebar-menu-button>
                                            </april:sidebar-menu-item>
                                            <div x-show="open" x-collapse class="space-y-1 pl-4 group-data-[collapsible=icon]:!hidden">
                                                @foreach ($menuItem['submenu'] as $submenu)
                                                    @if (!isset($submenu['can']) || auth()->user()->can($submenu['can']))
                                                        <april:sidebar-menu-item>
                                                            <april:sidebar-menu-button-link href="{{route($submenu['route'])}}" wire:navigate class="pl-3 {{Route::currentRouteName() == $submenu['route'] ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground' : ''}}">
                                                                <i class="w-4 shrink-0 text-center text-xs {{$submenu['icon'] ?? 'far fa-circle'}}" aria-hidden="true"></i>
                                                                <span>{{$submenu['text']}}</span>
                                                            </april:sidebar-menu-button-link>
                                                        </april:sidebar-menu-item>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <april:sidebar-menu-item>
                                            <april:sidebar-menu-button-link href="{{route($menuItem['route'])}}" wire:navigate class="{{Route::currentRouteName() == $menuItem['route'] ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground' : ''}}">
                                                <i class="w-4 shrink-0 text-center text-xs {{$menuItem['icon'] ?? 'fa fa-circle'}}" aria-hidden="true"></i>
                                                <span>{{$menuItem['text']}}</span>
                                            </april:sidebar-menu-button-link>
                                        </april:sidebar-menu-item>
                                    @endif
                                @endif
                            @endforeach
                        </april:sidebar-menu>
                    </april:sidebar-group-content>
                </april:sidebar-group>
            @endif
        @endforeach
    </slot:content>

    <slot:footer>
        <a href="{{route('profile.show')}}" class="flex h-10 items-center gap-2 rounded-md px-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:justify-center" wire:navigate>
            <img src="{{auth()->user()->profile_photo_url}}" alt="" class="h-8 w-8 rounded-full border object-cover">
            <span class="min-w-0 truncate group-data-[collapsible=icon]:hidden">{{auth()->user()->name}}</span>
        </a>
    </slot:footer>
</april:sidebar>
</div>
