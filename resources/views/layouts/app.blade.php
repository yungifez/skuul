<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{asset(config('app.favicon'))}}" type="image/x-icon">
    <title>
        @yield('title', config('app.name', 'Skuul'))
    </title>
    <x-partials.theme-script />
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <livewire:styles />
</head>

<body class="font-sans" data-ui="april">
    <a href="#main" class="sr-only">
        Skip to content
    </a>
    <div class="min-h-screen bg-background text-foreground">
        <april:sidebar-layout :default-open="sidebar_open()">
            <livewire:layouts.menu />
            <april:sidebar-inset>
                <livewire:layouts.header />
                <div class="border-b bg-background/95 px-4 py-5 backdrop-blur md:px-8">
                    <div class="mx-auto flex max-w-screen-2xl flex-col gap-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h1 class="text-2xl font-semibold tracking-tight md:text-3xl">@yield('page_heading')</h1>
                            <div class="flex flex-wrap items-center gap-3">
                                @yield('page_actions')
                                <div class="text-sm text-muted-foreground">
                                    {{ now()->format('D, M j, Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full">
                            @isset ($breadcrumbs)
                            <x-breadcrumbs :paths="$breadcrumbs" />
                            @endif
                        </div>
                    </div>
                </div>
                <main class="mx-auto w-full max-w-screen-2xl p-4 md:p-8" id="main">
                    @yield('content')
                </main>
            </april:sidebar-inset>
        </april:sidebar-layout>
    </div>
    @livewire('display-status')
</body>
@livewireScriptConfig
@stack('scripts')

</html>
