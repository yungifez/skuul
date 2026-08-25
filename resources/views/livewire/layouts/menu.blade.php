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
    {{-- Livewire renders this view on its own, so @aware cannot reach the
    sidebar-layout above it. Both sides read the stored state instead. --}}
    <april:sidebar x-persist="sidebar" collapsible="icon" :default-open="sidebar_open()"
        class="border-r max-h-svh sticky top-0">
        <slot:header>
            <div class="flex min-w-0 flex-col gap-2">
                <a href="{{route('home')}}" wire:navigate class="flex h-10 items-center gap-2" aria-label="Home">
                    <img src="{{asset(current_school()?->logoURL ?? config('app.logo'))}}" alt=""
                        class="h-8 w-8 rounded-md border object-cover">
                    <span class="truncate text-sm font-semibold">{{config('app.name')}}</span>
                </a>

                @if ($schools->count() > 1)
                    <form method="POST" action="{{ route('schools.setSchool') }}"
                        class="flex min-w-0 flex-col gap-1 group-data-[collapsible=icon]:hidden">
                        @csrf
                        <label for="sidebar-school-switcher"
                            class="px-1 text-[0.65rem] font-semibold uppercase text-muted-foreground">
                            Working school
                        </label>
                        <april:native-select id="sidebar-school-switcher" name="school_id"
                            aria-label="Working school" onchange="this.form.submit()">
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" @selected(current_school_id() === $school->id)>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </form>
                @elseif (current_school() !== null)
                    <span class="truncate px-1 text-xs text-muted-foreground group-data-[collapsible=icon]:hidden">
                        {{ current_school()->name }}
                    </span>
                @endif
            </div>
        </slot:header>

        <slot:content class="beautify-scrollbar" wire:navigate:scroll>
            @foreach ($menuGroups as $group)
            @if (collect($group['items'])->contains(fn (array $menuItem): bool => $menuItem['visible'] ?? true))
            <april:sidebar-group>
                <april:sidebar-group-label>{{$group['label']}}</april:sidebar-group-label>
                <april:sidebar-group-content>
                    <april:sidebar-menu>
                        @foreach ($group['items'] as $menuItem)
                        @if ($menuItem['visible'] ?? true)
                        @if (isset($menuItem['submenu']))
                        @php
                        $submenuIsOpen = in_array(Route::currentRouteName(), array_column($menuItem['submenu'],
                        'route'));
                        @endphp
                        <div x-data="{ open: @js($submenuIsOpen) }">
                            <april:sidebar-menu-item>
                                <april:sidebar-menu-button type="button" x-on:click="open = !open"
                                    x-bind:data-state="open ? 'open' : 'closed'">
                                    <x-icon :name="'lucide-'.($menuItem['icon'] ?? 'circle')" class="shrink-0" />
                                    <span>{{$menuItem['text']}}</span>
                                    <span class="ml-auto transition-transform group-data-[collapsible=icon]:!hidden"
                                        x-bind:class="{ '-rotate-90': !open }">
                                        <x-lucide-chevron-down class="size-3.5" />
                                    </span>
                                </april:sidebar-menu-button>
                            </april:sidebar-menu-item>
                            {{-- Cloak only the submenus that start closed. The open one must
                            paint straight away, or it flashes the other way round. --}}
                            <div x-show="open" x-collapse @if (!$submenuIsOpen) x-cloak @endif
                                class="space-y-1 pl-4 group-data-[collapsible=icon]:!hidden">
                                @foreach ($menuItem['submenu'] as $submenu)
                                @if ($submenu['visible'] ?? true)
                                <april:sidebar-menu-item>
                                    <april:sidebar-menu-button-link href="{{route($submenu['route'])}}"
                                        wire:navigate
                                        wire:current.exact="bg-sidebar-accent font-medium text-sidebar-accent-foreground"
                                        class="pl-3">
                                        <x-icon :name="'lucide-'.($submenu['icon'] ?? 'circle')" class="w-4 shrink-0" />
                                        <span>{{$submenu['text']}}</span>
                                    </april:sidebar-menu-button-link>
                                </april:sidebar-menu-item>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @else
                        <april:sidebar-menu-item>
                            <april:sidebar-menu-button-link
                                href="{{route($menuItem['route'], $menuItem['parameters'] ?? [])}}" wire:navigate
                                wire:current.exact="bg-sidebar-accent font-medium text-sidebar-accent-foreground">
                                <x-icon :name="'lucide-'.($menuItem['icon'] ?? 'circle')" class="w-4 shrink-0" />
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
            <a href="{{route('profile.show')}}"
                class="flex h-10 items-center gap-2 rounded-md px-2 text-sm hover:bg-sidebar-accent hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:justify-center"
                wire:navigate>
                <img src="{{auth()->user()->profile_photo_url}}" alt=""
                    class="h-8 w-8 rounded-full border object-cover">
                <span class="min-w-0 truncate group-data-[collapsible=icon]:hidden">{{auth()->user()->name}}</span>
            </a>
        </slot:footer>
    </april:sidebar>
</div>
