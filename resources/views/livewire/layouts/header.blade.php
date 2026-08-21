<header class="sticky top-0 z-30 flex h-14 w-full items-center justify-between border-b bg-background/95 px-4 backdrop-blur md:px-6">
    <div class="flex items-center gap-2">
        <april:sidebar-trigger />
        <a href="{{route('home')}}" class="flex items-center gap-3" aria-label="Home">
            <img src="{{asset(current_school()?->logoURL ?? config('app.logo'))}}" alt="" class="h-8 w-8 rounded-md border object-cover">
            <h1 class="hidden text-sm font-semibold tracking-tight sm:block">{{config('app.name')}}</h1>
        </a>
    </div>
    <div class="flex h-full items-center gap-1" x-data="{'darkMode' : $persist(false), 'fullScreen' : $persist(false) }">
        {{--full screen toggle--}}
        <april:button variant="ghost" size="icon" class="hidden sm:inline-flex" type="button" @click="fullScreen = !fullScreen; fullScreen == true ? document.documentElement.requestFullscreen() : document.exitFullscreen()">
            <i class="fa fa-expand text-sm" aria-hidden="true"></i>
            <p class="sr-only">Full screen mode</p>
        </april:button>
        {{--Dark mode toggle--}}
        <april:button variant="ghost" size="icon" type="button" @click="darkMode = !darkMode" x-effect="darkMode == true ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark') ">
            <i class="text-sm" :class="{'far fa-moon ' : darkMode == false, 'fas fa-moon' : darkMode == true}" aria-hidden="true"></i>
            <p class="sr-only">Dark mode</p>
        </april:button>
        {{--Click to open profile card--}}
        <april:dropdown-menu x-teleport="body">
            <slot:trigger>
                <april:button variant="ghost" size="none" class="ml-1 flex h-10 items-center gap-2 rounded-md px-2">
                    <april:avatar size="sm">
                        <slot:image src="{{auth()->user()->profile_photo_url}}" alt="{{auth()->user()->name}}" />
                        <slot:fallback>{{strtoupper(substr(auth()->user()->name, 0, 1))}}</slot:fallback>
                    </april:avatar>
                    <span class="hidden max-w-40 truncate text-left text-sm font-medium lg:block">{{auth()->user()->name}}</span>
                    <i class="fa fa-angle-down text-xs text-muted-foreground" aria-hidden="true"></i>
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
                            Academic year: {{current_school()->academicYear?->name}}<br>
                            Semester: {{current_school()->semester?->name}}
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
