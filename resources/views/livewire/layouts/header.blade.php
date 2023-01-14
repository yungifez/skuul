<header class="bg-white dark:bg-gray-800 shadow-md dark:shadow-white h-16 w-full flex justify-between items-center py-2 dark:text-white dark:border-b dark:border-white">
    <div class="flex items-center">
        <a href="#" class="text-2xl mx-3 dark:text-white text-gray-700 px-6" @click="menuOpen = !menuOpen">
            <p class="sr-only">Menu</p>
            <i class="fa fa-bars" aria-hidden="true"></i>
        </a>
        <a href="{{route('home')}}" class="hidden md:flex items-center justify-center">
            <img src="{{config('adminlte.logo_img')}}" alt="" class="rounded-full w-14 h-14 border border-gray-200 shadow-lg">
            <h1 class="text-lg font-semibold mx-3 text-center capitalize">{{config('app.name')}}</h1>
        </a>
    </div>
    <div class="flex justify-evenly items-center gap-6 px-5" id="dark-mode-switch">
        <button>
            <i class="far fa-moon text-xl" aria-hidden="true"></i>
            <p class="sr-only">Dark mode</p>
        </button>
        <button class="flex items-center">
            <img src="{{auth()->user()->defaultProfilePhotoUrl()}}" alt="" class="rounded-full w-10 h-10 border border-gray-200 shadow-md">
            <p class="hidden lg:block px-2">{{auth()->user()->name}}</p>
        </button>
    </div>
</header>