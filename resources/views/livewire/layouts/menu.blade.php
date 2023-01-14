<nav class="fixed lg:sticky inset-0 h-screen w-full lg:w-72 shadow-lg flex duration-250 text-gray-100 transition-all dark:lg:border-r dark:border-white" :class="{'w-0 overflow-hidden lg:flex lg:w-16' : menuOpen == false}">
    <aside class="w-4/6 md:w-3/6 lg:w-full bg-gray-900 shadow-md shadow-black border-x border-black">
        <a href="{{route('home')}}" class="flex md:hidden items-center justify-center border-b border-gray-200 p-4">
            <img src="{{config('adminlte.logo_img')}}" alt="" class="rounded-full w-14 h-14 border border-gray-200 shadow-md">
            <h1 class="text-lg font-semibold mx-3 text-center capitalize">{{config('app.name')}}</h1>
        </a>
    </aside>
    <div class="lg:hidden w-2/6 md:w-3/6 bg-gray-600 opacity-30" @click="menuOpen = false" x-show="menuOpen" x-transition:enter="transition-all ease-in duration-200 delay-250" x-transition:enter-start="opacity-0" >
       
    </div>
</nav>
