@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Transcripts', 'active'],
]])

@section('title', 'Transcripts')
@section('page_heading', 'Transcripts')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <april:alert>
                <slot:title>Issued</slot:title>
                <slot:description>{{ session('success') }}</slot:description>
            </april:alert>
        @endif

        @if ($errors->has('transcript'))
            <april:alert variant="destructive">
                <slot:title>The transcript was not issued</slot:title>
                <slot:description>{{ $errors->first('transcript') }}</slot:description>
            </april:alert>
        @endif

        @can('create', App\Models\TranscriptSnapshot::class)
            <april:card>
                <slot:title>Issue a transcript</slot:title>
                <slot:description>A transcript is the lifetime academic record. It copies the latest official result of every subject the learner took. It never changes once issued, so a correction goes out as the next revision.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('transcripts.store') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="transcript-student">Learner</april:label>
                            <april:native-select id="transcript-student" name="student_record_id" required>
                                <option value="">Choose a learner</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_record_id') == $student->id)>
                                        {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            @error('student_record_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2 lg:col-span-2">
                            <april:label for="transcript-reason">Reason for a revision</april:label>
                            <april:input id="transcript-reason" name="reason" value="{{ old('reason') }}"
                                placeholder="Only needed when reissuing" />
                        </div>

                        <april:button type="submit">
                            <x-lucide-scroll-text class="mr-2 size-4" />
                            Issue transcript
                        </april:button>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>Find a transcript</slot:title>
            <slot:description>Narrow the list to one learner.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('transcripts.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="filter-student">Learner</april:label>
                        <april:native-select id="filter-student" name="student_record_id">
                            <option value="">Every learner</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected($selectedStudent === $student->id)>
                                    {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedStudent !== null)
                            <april:button-link href="{{ route('transcripts.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Issued transcripts</slot:title>
            <slot:description>Open a row to read the subjects the transcript carries.</slot:description>
            <slot:content>
                @if ($transcripts->isEmpty())
                    @if ($selectedStudent !== null)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No transcript has been issued for that learner.">
                            <april:button-link href="{{ route('transcripts.index') }}" variant="outline">Show every transcript</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-scroll-text" title="No transcripts yet"
                            description="Issue one above once a learner has at least one published subject result." />
                    @endif
                @else
                    <div x-data="{ open: null }">
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Learner</april:data-table-head>
                                    <april:data-table-head>Revision</april:data-table-head>
                                    <april:data-table-head>Subjects</april:data-table-head>
                                    <april:data-table-head>Issued</april:data-table-head>
                                    <april:data-table-head class="text-right">Actions</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($transcripts as $transcript)
                                    @php
                                        $results = $transcript->payload['results'] ?? [];
                                        $grouped = collect($results)->groupBy(fn (array $result): string => $result['academic_year'].' · '.$result['academic_period']);
                                    @endphp

                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $transcript->studentRecord->user?->name ?? $transcript->studentRecord->admission_number }}
                                            <span class="block text-xs text-muted-foreground">{{ $transcript->studentRecord->admission_number }}</span>
                                        </april:data-table-cell>
                                        <april:data-table-cell>
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                Revision {{ $transcript->revision }}
                                            </span>
                                        </april:data-table-cell>
                                        <april:data-table-cell>{{ count($results) }}</april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $transcript->issued_at->format('j M Y') }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            <april:button variant="outline" size="sm" type="button"
                                                x-on:click="open = (open === {{ $transcript->id }} ? null : {{ $transcript->id }})">
                                                <span x-text="open === {{ $transcript->id }} ? 'Hide subjects' : 'Read subjects'">Read subjects</span>
                                            </april:button>
                                        </april:data-table-cell>
                                    </april:data-table-row>

                                    <april:data-table-row x-show="open === {{ $transcript->id }}" style="display: none">
                                        <april:data-table-cell colspan="5" class="bg-muted/30">
                                            <div class="space-y-4">
                                                @if (filled($transcript->reason))
                                                    <p class="text-sm text-muted-foreground">Reason for this revision: {{ $transcript->reason }}</p>
                                                @endif

                                                @foreach ($grouped as $heading => $groupResults)
                                                    <div>
                                                        <p class="text-sm font-medium">{{ $heading }}</p>
                                                        <ul class="mt-2 divide-y rounded-md border bg-background">
                                                            @foreach ($groupResults as $result)
                                                                <li class="flex items-center justify-between px-3 py-2 text-sm">
                                                                    <span>{{ $result['subject'] }}</span>
                                                                    <span class="text-muted-foreground">
                                                                        {{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    </div>

                    <div class="pt-4">
                        {{ $transcripts->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
