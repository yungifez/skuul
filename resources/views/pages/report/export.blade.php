{{-- A report printed as a document. This page stands on its own: it is
     rendered by a worker, which has no signed-in user to read a school from. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { font-family: Helvetica, sans-serif; }
        body { background-color: white; color: #111; }
        h1 { font-size: 1.4rem; margin: 0 0 0.25rem; }
        .built { color: #555; font-size: 0.75rem; margin: 0 0 1rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        th, td { border: 1px solid #999; padding: 0.35rem 0.5rem; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: 700; }
        tr { page-break-inside: avoid; }
        .empty { color: #555; font-style: italic; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="built">Built {{ now()->format('j M Y, H:i') }} &middot; {{ trans_choice(':count row|:count rows', $rows->count(), ['count' => $rows->count()]) }}</p>

    @if ($rows->isEmpty())
        <p class="empty">This report has nothing to show.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
