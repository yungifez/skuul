<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f4f5;
            color: #18181b;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        .print-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin: 1rem auto;
            max-width: 1100px;
        }
        .print-toolbar a,
        .print-toolbar button {
            border: 1px solid #d4d4d8;
            border-radius: 0.5rem;
            background: #fff;
            color: #18181b;
            cursor: pointer;
            font: inherit;
            padding: 0.55rem 0.85rem;
            text-decoration: none;
        }
        .print-toolbar button {
            border-color: #18181b;
            background: #18181b;
            color: #fff;
        }
        .print-document {
            width: min(100% - 2rem, 1100px);
            margin: 0 auto 2rem;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 8px 30px rgb(24 24 27 / 0.08);
        }
        header {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e4e4e7;
            padding-bottom: 1rem;
        }
        .logo { width: 72px; height: 72px; border-radius: 0.75rem; object-fit: cover; }
        .site-identity h1, .site-identity h2 { margin: 0; text-align: left; }
        .site-identity h1 { font-size: 1.35rem; }
        .site-identity h2 { margin-top: 0.25rem; color: #71717a; font-size: 0.9rem; font-weight: 400; }
        main { width: 100%; }
        table, th, td { border: 1px solid #d4d4d8; border-collapse: collapse; }
        table { width: 100%; vertical-align: middle; text-align: left; }
        th { background: #f4f4f5; font-weight: 700; }
        td, th { padding: 0.65rem 0.75rem; }
        @media print {
            @page { margin: 12mm; }
            body { background: #fff; }
            .print-toolbar { display: none !important; }
            .print-document { width: 100%; margin: 0; padding: 0; box-shadow: none; }
            header { margin-bottom: 1rem; }
            a { color: inherit; text-decoration: none; }
            form, button, select, textarea, input, [wire\:loading] { display: none !important; }
        }
    </style>
    @yield('style')
</head>
<body>
    <div class="print-toolbar" data-print-toolbar>
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" data-print-button>Print</button>
    </div>

    <div class="print-document">
        <header>
            <div>
                <img src="{{ current_school()->logoURL ?? asset(config('app.logo')) }}" alt="{{ current_school()->name }}" class="logo">
            </div>
            <div class="site-identity">
                <h1>{{ current_school()->name }}</h1>
                <h2>{{ current_school()->address }}</h2>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <script>
        document.querySelector('[data-print-button]')?.addEventListener('click', () => window.print());
    </script>
</body>
</html>
