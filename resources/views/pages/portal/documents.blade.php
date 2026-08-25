@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Documents', 'active'],
]])

@section('title', 'Documents')
@section('page_heading', 'Official documents')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm text-muted-foreground">Published documents for {{ $studentRecord->user?->name ?? $studentRecord->admission_number }} at {{ $studentRecord->school->name }}.</p>
        </div>

        <april:card>
            <slot:title>Report cards</slot:title>
            <slot:description>Each row is the latest published revision for one academic period.</slot:description>
            <slot:content>
                @if ($reportCards->isEmpty())
                    <x-empty-state icon="lucide-file-chart-column" title="No report cards yet"
                        description="The school will publish a report card here when it is ready." />
                @else
                    <div class="space-y-3">
                        @foreach ($reportCards as $reportCard)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-4">
                                <div>
                                    <p class="font-medium">{{ $reportCard->academicPeriod->label ?? $reportCard->academicPeriod->name }}</p>
                                    <p class="text-sm text-muted-foreground">Revision {{ $reportCard->revision }} · published {{ $reportCard->published_at->format('j M Y') }}</p>
                                </div>
                                <april:button-link href="{{ route('portal.documents.report-cards.download', [$studentRecord, $reportCard]) }}" variant="outline" size="sm"><x-lucide-download class="mr-2 size-4" />Download</april:button-link>
                            </div>
                        @endforeach
                    </div>
                @endif
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Transcript</slot:title>
            <slot:description>The latest issued lifetime academic record.</slot:description>
            <slot:content>
                @if ($transcript === null)
                    <x-empty-state icon="lucide-scroll-text" title="No transcript yet"
                        description="The school will issue a transcript when the record is ready." />
                @else
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-4">
                        <div>
                            <p class="font-medium">Revision {{ $transcript->revision }}</p>
                            <p class="text-sm text-muted-foreground">Issued {{ $transcript->issued_at->format('j M Y') }}</p>
                        </div>
                        <april:button-link href="{{ route('portal.documents.transcripts.download', [$studentRecord, $transcript]) }}" variant="outline" size="sm"><x-lucide-download class="mr-2 size-4" />Download</april:button-link>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
