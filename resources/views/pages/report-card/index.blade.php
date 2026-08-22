@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['text' => 'Report cards', 'active']]])

@section('title', 'Report cards')
@section('page_heading', 'Report cards')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('report_card'))<div class="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">{{ $errors->first('report_card') }}</div>@endif
        @can('create', App\Models\ReportCardSnapshot::class)
            <april:card><slot:title>Publish a report card</slot:title><slot:description>Create an official, immutable cross-subject record from the latest published subject results.</slot:description><slot:content>
                <form method="POST" action="{{ route('report-cards.store') }}" class="grid gap-3 md:grid-cols-3">@csrf
                    <select name="student_record_id" required class="rounded-md border border-input bg-background px-3 py-2 text-sm"><option value="">Choose learner</option>@foreach ($students as $student)<option value="{{ $student->id }}">{{ $student->user?->name ?? $student->admission_number }} · {{ $student->admission_number }}</option>@endforeach</select>
                    <select name="academic_period_id" required class="rounded-md border border-input bg-background px-3 py-2 text-sm"><option value="">Choose period</option>@foreach ($periods as $period)<option value="{{ $period->id }}">{{ $period->label ?? $period->name }}</option>@endforeach</select>
                    <div class="flex gap-3"><input name="reason" class="min-w-0 flex-1 rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Reason for revision (optional)"><april:button type="submit">Publish</april:button></div>
                </form>
            </slot:content></april:card>
        @endcan
        <april:card><slot:title>Published cards</slot:title><slot:description>Each version remains exactly as issued, even after a later correction.</slot:description><slot:content>
            @if ($reportCards->isEmpty())<p class="py-6 text-sm text-muted-foreground">No report cards have been published yet.</p>@else
                <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b text-left text-muted-foreground"><tr><th class="px-3 py-2">Learner</th><th class="px-3 py-2">Period</th><th class="px-3 py-2">Average</th><th class="px-3 py-2">Revision</th><th class="px-3 py-2">Published</th></tr></thead><tbody class="divide-y">@foreach ($reportCards as $card)<tr><td class="px-3 py-3"><a class="font-medium underline" href="{{ route('report-cards.show', $card) }}">{{ $card->studentRecord->user?->name ?? $card->studentRecord->admission_number }}</a></td><td class="px-3 py-3">{{ $card->academicPeriod->label ?? $card->academicPeriod->name }}</td><td class="px-3 py-3">{{ $card->average_percentage === null ? '—' : number_format($card->average_percentage, 2).'%' }}</td><td class="px-3 py-3">{{ $card->revision }}</td><td class="px-3 py-3">{{ $card->published_at->format('j M Y') }}</td></tr>@endforeach</tbody></table></div>
            @endif
        </slot:content></april:card>
    </div>
@endsection
