<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>
            @yield('title', config('app.name', 'Skuul'))
        </title>
        @vite('resources/css/app.css')
        <livewire:styles />
    </head>
    <body class="font-sans">
        <div x-data="{ menuOpen: window.innerWidth >=  1024 ? $persist(false) : false }">
            <livewire:layouts.header/>
            <div class="lg:flex lg:flex-cols text-gray-900 bg-gray-100 dark:bg-gray-700 dark:text-gray-50 min-h-screen" >
                <livewire:layouts.menu />
                <main class="w-full max-w-full overflow-scroll beautify-scrollbar">
                    <div class="bg-white dark:bg-gray-800 p-4 w-inherit  ">
                        <h1 class="text-2xl md:text-3xl my-2 capitalize font-semibold">@yield('page_heading')</h1>
                        <x-show-set-school />
                        @isset ($breadcrumbs)
                            <x-breadcrumbs :paths="$breadcrumbs"/>
                        @endif
                    </div>
                    <div class="p-4">
                        @yield('content') 
                    </div>
                </main>
            </div>
        </div> 
    @livewire('display-status')
    </body>
    <livewire:scripts />
    @vite(['resources/js/app.js'])
    @stack('scripts')
</html>