<header class="bg-white dark:bg-gray-800 shadow-md dark:shadow-white h-16 w-full flex justify-between items-center py-2 dark:text-white border-b-2 dark:border-white">
    <div class="flex items-center">
        <button role="button" class="text-2xl mx-3 dark:text-white text-gray-700 px-6" @click="menuOpen = !menuOpen">
            <p class="sr-only">Menu</p>
            <i class="fa fa-bars" aria-hidden="true"></i>
        </button>
        <a href="{{route('home')}}" class="hidden md:flex items-center justify-center" aria-label="Home">
            <img src="{{asset(auth()->user()->school->logoURL ?? config('app.logo'))}}" alt="" class="rounded-full w-12 h-12 border border-gray-200 shadow-lg">
            <h1 class="text-lg font-semibold mx-3 text-center capitalize">{{config('app.name')}}</h1>
        </a>
    </div>
    <div class="flex justify-evenly items-center gap-6 px-5 h-full" x-data="{'dropDownOpen': false, 'darkMode' : $persist(false), 'fullScreen' : $persist(false) }">
        {{--full screen toggle--}}
        <button @click="fullScreen = !fullScreen; fullScreen == true ? document.documentElement.requestFullscreen() :  document.exitFullscreen()">
            <i class="fa fa-expand text-xl" aria-hidden="true"></i>
            <p class="sr-only">Full screen mode</p>
        </button>
        {{--Dark mode toggle--}}
        <button @click="darkMode = !darkMode" x-effect="darkMode == true ? document.body.classList.add('dark') :  document.body.classList.remove('dark') ">
            <i class="text-xl" :class="{'far fa-moon ' : darkMode == false, 'fas fa-moon' : darkMode == true}" aria-hidden="true"></i>
            <p class="sr-only">Dark mode</p>
        </button>
        {{--Click to open profile card--}}
        <button class="h-full flex items-center gap-2"  @click="dropDownOpen = !dropDownOpen">
            <p class="sr-only">Open Profile Card</p>
            <div class="flex items-center h-full">
                <img src="{{auth()->user()->profile_photo_url}}" alt="" class="rounded-full w-10 h-10 border border-gray-200 shadow-md">
                <p class="hidden lg:block px-2"  >{{auth()->user()->name}}</p>
            </div>
            <i :class="{'transition-all' : true,'fas fa-angle-right' : dropDownOpen == false , 'fas fa-angle-down ' : dropDownOpen == true}" aria-hidden="true"></i>
        </button>
        {{--User profile card--}}
        <div class="absolute bg-blue-700 dark:bg-gray-800 top-16 w-5/6 border  md:w-2/6 lg:w-1/5 shadow-md right-2 flex flex-col items-center justify-center rounded p-4 text-white " x-show="dropDownOpen" x-transition style="display: none">
            <img src="{{auth()->user()->profile_photo_url}}" alt="" class="rounded-full w-20 h-20 border border-gray-200 shadow-md">
            <h2 class="text-lg  font-bold">{{auth()->user()->name}}</h2>
            <p class="text-center">
                @isset(auth()->user()->school)
                    Academic year: {{auth()->user()->school->academicYear?->name}} <br>
                    Semester: {{auth()->user()->school->semester?->name}}
                @endif
            </p>
            <form action="{{route('logout')}}" class="w-full" method="POST">
                @csrf
                <button href="" class="w-full bg-white text-gray-900 p-3 mt-3 text-center"><i class="fa fa-power-off text-red-700 px-2" aria-hidden="true"></i>Log out</button>
            </form>
        </div>
    </div>
</header>