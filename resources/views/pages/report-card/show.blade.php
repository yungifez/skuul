@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['href' => route('report-cards.index'), 'text' => 'Report cards'], ['text' => 'Issued card', 'active']]])

@section('title', 'Issued report card')
@section('page_heading', 'Issued report card')

@section('page_actions')
    <april:button-link href="{{ route('report-cards.index') }}" variant="outline">Back to report cards</april:button-link>
@endsection

@section('content')
    <div class="space-y-6"><april:card><slot:title>{{ $reportCardSnapshot->studentRecord->user?->name ?? $reportCardSnapshot->studentRecord->admission_number }}</slot:title><slot:description>{{ $reportCardSnapshot->academicPeriod->label ?? $reportCardSnapshot->academicPeriod->name }} · Revision {{ $reportCardSnapshot->revision }} · Issued {{ $reportCardSnapshot->published_at->format('j M Y') }}</slot:description><slot:content><div class="text-2xl font-semibold">{{ $reportCardSnapshot->average_percentage === null ? 'No overall percentage' : number_format($reportCardSnapshot->average_percentage, 2).'%' }}</div></slot:content></april:card><april:card><slot:title>Subject results</slot:title><slot:content><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b text-left text-muted-foreground"><tr><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Result</th><th class="px-3 py-2">Source revision</th></tr></thead><tbody class="divide-y">@foreach ($reportCardSnapshot->payload['results'] as $result)<tr><td class="px-3 py-3">{{ $result['subject']['name'] }}</td><td class="px-3 py-3">{{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}</td><td class="px-3 py-3">{{ $result['source_revision'] }}</td></tr>@endforeach</tbody></table></div></slot:content></april:card></div>
@endsection
