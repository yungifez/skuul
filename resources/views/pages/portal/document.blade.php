<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ $studentRecord->user?->name ?? $studentRecord->admission_number }}</title>
    <style>
        body { color: #111827; font-family: sans-serif; margin: 3rem auto; max-width: 52rem; }
        h1 { margin-bottom: .25rem; }
        .muted { color: #6b7280; }
        .meta { border-bottom: 1px solid #d1d5db; margin-bottom: 1.5rem; padding-bottom: 1rem; }
        table { border-collapse: collapse; margin-top: 1rem; width: 100%; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: .65rem .4rem; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
    </style>
</head>
<body>
    <div class="meta">
        <h1>{{ $title }}</h1>
        <p class="muted">{{ $studentRecord->user?->name ?? 'Learner' }} · {{ $studentRecord->admission_number }} · {{ $studentRecord->school->name }}</p>
    </div>

    @if ($type === 'report-card')
        <p><strong>Academic year:</strong> {{ $document->academicYear?->name ?? 'Unknown year' }}</p>
        <p><strong>Period:</strong> {{ $document->academicPeriod->label ?? $document->academicPeriod->name }}</p>
        <p><strong>Revision:</strong> {{ $document->revision }} · <strong>Published:</strong> {{ $document->published_at->format('j M Y') }}</p>
        <p><strong>Average:</strong> {{ $document->average_percentage === null ? '—' : number_format($document->average_percentage, 2).'%' }}</p>
        <table>
            <thead><tr><th>Subject</th><th>Percentage</th></tr></thead>
            <tbody>
                @foreach ($document->payload['results'] ?? [] as $result)
                    <tr><td>{{ $result['subject']['name'] }}</td><td>{{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p><strong>Revision:</strong> {{ $document->revision }} · <strong>Issued:</strong> {{ $document->issued_at->format('j M Y') }}</p>
        <table>
            <thead><tr><th>Period</th><th>Subject</th><th>Percentage</th></tr></thead>
            <tbody>
                @foreach ($document->payload['results'] ?? [] as $result)
                    <tr><td>{{ $result['academic_year'] }} · {{ $result['academic_period'] }}</td><td>{{ $result['subject'] }}</td><td>{{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
