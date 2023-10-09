<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{asset(config('app.favicon'))}}" type="image/x-icon">
        <title>
            @yield('title', config('app.name', 'Skuul'))
        </title>

        <!-- Styles -->
        @vite('resources/css/app.css')
        <livewire:styles />
        
        <!--Shortcut icon-->
        <link rel="shortcut icon" href="favicons/favicon.ico" type="image/x-icon">
    </head>
    <body class="bg-gray-100">
        @yield('body')
        <livewire:display-status />
    </body>
    <livewire:scripts />
</html>