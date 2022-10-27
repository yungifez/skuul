<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/dashboard.css') }}">
        @livewireStyles
        
        <!--Shortcut icon-->
        <link rel="shortcut icon" href="favicons/favicon.ico" type="image/x-icon">

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="{{ mix('js/dashboard.js') }}" defer></script>
    </head>
    <body class="bg-light font-sans antialiased">
        {{ $slot }}
    </body>
    @livewireScripts
    
</html>