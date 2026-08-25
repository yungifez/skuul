<header
    class="sticky top-0 z-30 flex h-14 w-full items-center justify-between gap-2 border-b bg-background/95 px-4 backdrop-blur md:px-6">
    <div class="flex min-w-0 items-center gap-2">
        <april:sidebar-trigger />
        <a href="{{route('home')}}" class="flex shrink-0 items-center gap-3" aria-label="Home">
            <h1 class="hidden text-sm font-semibold tracking-tight sm:block">{{config('app.name')}}</h1>
        </a>
        <x-show-set-school />
    </div>
    <div class="flex h-full shrink-0 items-center gap-1" x-data="{'fullScreen' : $persist(false) }">
        {{--full screen toggle--}}
        <april:button variant="ghost" size="icon" class="hidden sm:inline-flex" type="button"
            @click="fullScreen = !fullScreen; fullScreen == true ? document.documentElement.requestFullscreen() : document.exitFullscreen()">
            <x-lucide-maximize class="size-4" />
            <p class="sr-only">Full screen mode</p>
        </april:button>
        {{--Dark mode toggle--}}
        <april:dropdown-menu>
            <slot:trigger>
                <april:button aria-label="open theme selection" class="justify-center" size="icon" variant="ghost"
                    type="button">
                    <x-lucide-sun class="h-4 w-4 dark:hidden" />
                    <x-lucide-moon class="hidden h-4 w-4 dark:block" />
                </april:button>
            </slot:trigger>
            <slot:content class="w-40">
                <april:dropdown-menu-item aria-label="Select light theme" size="sm" type="button"
                    class="w-full justify-start focus-visible:outline-hidden" x-on:click="setTheme('light')">
                    <x-lucide-sun class="mr-2 h-4 w-4" />
                    <p class="text-sm">Light</p>
                </april:dropdown-menu-item>
                <april:dropdown-menu-item aria-label="Select dark theme" size="sm" type="button"
                    class="w-full justify-start focus-visible:outline-hidden" x-on:click="setTheme('dark')">
                    <x-lucide-moon class="mr-2 h-4 w-4" />
                    <p class="text-sm">Dark</p>
                </april:dropdown-menu-item>
                <april:dropdown-menu-item aria-label="Set theme based on system preference" size="sm" type="button"
                    class="w-full justify-start focus-visible:outline-hidden" x-on:click="setTheme('system')">
                    <x-lucide-monitor class="mr-2 h-4 w-4" />
                    <p class="text-sm">System</p>
                </april:dropdown-menu-item>
            </slot:content>
        </april:dropdown-menu>
        {{--Click to open profile card--}}
        <april:dropdown-menu x-teleport="body">
            <slot:trigger>
                <april:button variant="ghost" size="none" class="ml-1 flex h-10 items-center gap-2 rounded-md px-2">
                    <april:avatar size="sm">
                        <slot:image src="{{auth()->user()->profile_photo_url}}" alt="{{auth()->user()->name}}" />
                        <slot:fallback>{{strtoupper(substr(auth()->user()->name, 0, 1))}}</slot:fallback>
                    </april:avatar>
                    <span
                        class="hidden max-w-40 truncate text-left text-sm font-medium lg:block">{{auth()->user()->name}}</span>
                    <x-lucide-chevron-down class="size-3.5 text-muted-foreground" />
                </april:button>
            </slot:trigger>
            <slot:content class="right-4 top-14 w-72 md:right-6">
                <div class="flex items-center gap-3 border-b p-3">
                    <div>
                        <p class="font-medium">{{auth()->user()->name}}</p>
                        <p class="text-xs text-muted-foreground">{{auth()->user()->email}}</p>
                    </div>
                </div>
                <div class="p-1">
                    <p class="px-2 py-1.5 text-xs text-muted-foreground">
                        @if (current_school() !== null)
                        Academic year: {{current_academic_year()?->name}}<br>
                        AcademicPeriod: {{current_academic_period()?->name}}
                        @endif
                    </p>
                    <form action="{{route('logout')}}" method="POST">
                        @csrf
                        <april:dropdown-menu-item type="submit">Log out</april:dropdown-menu-item>
                    </form>
                </div>
            </slot:content>
        </april:dropdown-menu>
    </div>
</header>
