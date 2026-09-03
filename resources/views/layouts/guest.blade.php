<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ asset(config('app.favicon')) }}">
        <title>
            @yield('title', config('app.name', 'Skuul'))
        </title>

        <x-partials.theme-script />

        <!-- Styles -->
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')
        <livewire:styles />
    </head>
    <body class="min-h-screen bg-background text-foreground" data-ui="april">
        @yield('body')
        <livewire:display-status />
    </body>
    @livewireScriptConfig
</html>
