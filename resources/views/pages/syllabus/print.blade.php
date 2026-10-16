@extends('layouts.print')

@section('title', $syllabus->name)

@section('back_url', route('syllabi.show', $syllabus))

@section('content')
    @php($courseOffering = $syllabus->courseOffering)
    <h2 class="scheme-title">Scheme of work: {{ $syllabus->name }}</h2>
    <dl class="scheme-details">
        <div><dt>Subject</dt><dd>{{ $courseOffering->subject->name }}</dd></div>
        <div><dt>Class</dt><dd>{{ $courseOffering->academicLevel->name }}</dd></div>
        <div><dt>{{ school_term('period', 'Academic period') }}</dt><dd>{{ $courseOffering->academicPeriod->academicYear?->name }} · {{ $courseOffering->academicPeriod->label ?? $courseOffering->academicPeriod->name }}</dd></div>
        <div><dt>Revision</dt><dd>{{ $syllabus->revision }} · {{ $syllabus->status->label() }}@if ($syllabus->published_at) on {{ $syllabus->published_at->toFormattedDateString() }}@endif</dd></div>
    </dl>

    @if ($syllabus->description)
        <p class="scheme-overview">{{ $syllabus->description }}</p>
    @endif

    @if ($syllabus->topics->isEmpty())
        <p>No weekly topics are planned in this syllabus yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th scope="col">Week</th>
                    <th scope="col">Topic</th>
                    <th scope="col">Objectives</th>
                    <th scope="col">Content</th>
                    <th scope="col">Resources</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($syllabus->topics as $topic)
                    <tr>
                        <td>{{ $topic->week ?? '—' }}</td>
                        <td>{{ $topic->title }}</td>
                        <td class="multiline">{{ $topic->objectives }}</td>
                        <td class="multiline">{{ $topic->content }}</td>
                        <td class="multiline">{{ $topic->resources }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

@section('style')
    <style>
        .scheme-title { margin: 0 0 1rem; font-size: 1.2rem; }
        .scheme-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr)); gap: 0.5rem 1.5rem; margin: 0 0 1rem; }
        .scheme-details dt { color: #71717a; font-size: 0.8rem; }
        .scheme-details dd { margin: 0; font-weight: 600; }
        .scheme-overview { margin: 0 0 1rem; white-space: pre-line; }
        td { vertical-align: top; }
        .multiline { white-space: pre-line; }
        tr { break-inside: avoid; }
        @page { size: landscape; }
    </style>
@endsection
