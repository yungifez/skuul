@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('report-cards.index'), 'text' => 'Report cards', 'active'],
]])

@section('title', 'Report cards')
@section('page_heading', 'Report cards')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <april:alert>
                <slot:title>Published</slot:title>
                <slot:description>{{ session('success') }}</slot:description>
            </april:alert>
        @endif

        @if ($errors->has('report_card'))
            <april:alert variant="destructive">
                <slot:title>The report card was not published</slot:title>
                <slot:description>{{ $errors->first('report_card') }}</slot:description>
            </april:alert>
        @endif

        @can('create', App\Models\ReportCardSnapshot::class)
            <april:card>
                <slot:title>Publish a report card</slot:title>
                <slot:description>A report card is an official record. It is built from the results already published for the period, and it never changes once issued. Publishing again creates the next revision.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('report-cards.store') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-student">Learner</april:label>
                            <april:native-select id="report-card-student" name="student_record_id" required>
                                <option value="">Choose a learner</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_record_id') == $student->id)>
                                        {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            @error('student_record_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-period">{{ school_term('period', 'Academic period') }}</april:label>
                            <april:native-select id="report-card-period" name="academic_period_id" required>
                                <option value="">Choose a {{ school_term('period', 'period') }}</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}" @selected(old('academic_period_id') == $period->id)>
                                        {{ $period->label ?? $period->name }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            @error('academic_period_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-reason">Reason for a revision</april:label>
                            <april:input id="report-card-reason" name="reason" value="{{ old('reason') }}"
                                placeholder="Only needed when reissuing" />
                        </div>

                        <april:button type="submit">
                            <x-lucide-file-check class="mr-2 size-4" />
                            Publish
                        </april:button>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>Find a card</slot:title>
            <slot:description>Narrow the list to one learner or one {{ school_term('period', 'period') }}.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('report-cards.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
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

                    <div class="flex flex-col gap-2">
                        <april:label for="filter-period">{{ school_term('period', 'Academic period') }}</april:label>
                        <april:native-select id="filter-period" name="academic_period_id">
                            <option value="">Every {{ school_term('period', 'period') }}</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" @selected($selectedPeriod === $period->id)>
                                    {{ $period->label ?? $period->name }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedStudent !== null || $selectedPeriod !== null)
                            <april:button-link href="{{ route('report-cards.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Published cards</slot:title>
            <slot:description>Each version stays exactly as it was issued, even after a later correction.</slot:description>
            <slot:content>
                @if ($reportCards->isEmpty())
                    @if ($selectedStudent !== null || $selectedPeriod !== null)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No report card was published for that learner and period.">
                            <april:button-link href="{{ route('report-cards.index') }}" variant="outline">Show every card</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-file-text" title="No report cards yet"
                            description="Publish one above once a period has results. It becomes an official record you can reissue but never edit." />
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Learner</april:data-table-head>
                                <april:data-table-head>{{ school_term('period', 'Period') }}</april:data-table-head>
                                <april:data-table-head>Average</april:data-table-head>
                                <april:data-table-head>Revision</april:data-table-head>
                                <april:data-table-head>Published</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($reportCards as $card)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $card->studentRecord->user?->name ?? $card->studentRecord->admission_number }}
                                        <span class="block text-xs text-muted-foreground">{{ $card->studentRecord->admission_number }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $card->academicPeriod->label ?? $card->academicPeriod->name }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        {{ $card->average_percentage === null ? '—' : number_format($card->average_percentage, 2).'%' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            Revision {{ $card->revision }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $card->published_at->format('j M Y') }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('report-cards.show', $card) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            View
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $reportCards->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
