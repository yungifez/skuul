{{--
    The page a person sees when a request fails.

    It uses only the app's stylesheet and plain markup, with no Livewire and no
    database, so it still renders when the error came from one of them. Each
    page names what happened in plain words and offers a way back.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>@yield('title') · {{ config('app.name', 'Skuul') }}</title>
        <x-partials.theme-script />
        @vite('resources/css/app.css')
    </head>
    <body class="min-h-screen bg-background text-foreground antialiased">
        <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-center gap-6 px-4 py-10">
            <p class="text-sm font-medium tabular-nums text-muted-foreground">@yield('code')</p>
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold tracking-tight">@yield('title')</h1>
                <p class="text-muted-foreground">@yield('message')</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                @section('actions')
                    <a href="{{ url('/') }}" class="inline-flex h-11 select-none items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90">Go to the dashboard</a>
                    <button type="button" onclick="history.back()" class="inline-flex h-11 select-none items-center justify-center rounded-md px-4 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-accent-foreground">Go back</button>
                @show
            </div>
        </main>
    </body>
</html>
