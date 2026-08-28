@php
    $copies = $preview['copies'] ?? null;
    $skips = $preview['skips'] ?? null;
    $problems = $preview['problems'] ?? null;
    $setupMode = request()->boolean('setup');
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.roll-forward.show'), 'text' => 'Roll over subjects', 'active'],
]])

@section('title', 'Roll over subjects')
@section('page_heading', 'Roll over subjects')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <april:card>
            <slot:title>Start this year from last year's subjects</slot:title>
            <slot:description>Each previous offering becomes its own level-specific draft. Planned periods, capacity, learner grouping, and matching sections are carried forward; learners, teachers, marks, and results are not.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('course-offerings.roll-forward.show') }}" class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
                    @if ($setupMode)
                        <input type="hidden" name="setup" value="1">
                    @endif
                    <div class="flex flex-col gap-2">
                        <april:label for="source-year">Copy from</april:label>
                        <select id="source-year" name="source_academic_year_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a school year</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected($source?->id === $academicYear->id)>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="target-year">Create in</april:label>
                        <select id="target-year" name="target_academic_year_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a school year</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected($target?->id === $academicYear->id)>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <april:button type="submit" variant="outline">Review subjects</april:button>
                </form>
            </slot:content>
        </april:card>

        @if ($problem)
            <april:alert variant="destructive">
                <slot:title>That rollover cannot be completed</slot:title>
                <slot:description>{{ $problem }}</slot:description>
            </april:alert>
        @elseif ($preview !== null)
            <april:card>
                <slot:title>Review the subjects to copy</slot:title>
                <slot:description>{{ $source->name }} → {{ $target->name }}. Nothing is created until you confirm.</slot:description>
                <slot:content class="space-y-6">
                    @if ($copies->isNotEmpty())
                        <div>
                            <h2 class="mb-3 text-sm font-semibold">Will be created as drafts</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] text-sm">
                                    <thead class="border-b text-left text-muted-foreground">
                                        <tr><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Level</th><th class="px-3 py-2">Period</th><th class="px-3 py-2">Roster</th><th class="px-3 py-2">Settings</th></tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach ($copies as $copy)
                                            <tr>
                                                <td class="px-3 py-3 font-medium">{{ $copy['offering']->subject->name }}</td>
                                                <td class="px-3 py-3">{{ $copy['offering']->academicLevel->name }}</td>
                                                <td class="px-3 py-3">{{ $copy['period']->display_name }}</td>
                                                <td class="px-3 py-3">{{ school_roster_label($copy['offering']->roster_mode) }}</td>
                                                <td class="px-3 py-3 text-muted-foreground">
                                                    {{ $copy['offering']->planned_periods_per_week ? $copy['offering']->planned_periods_per_week.' periods/week' : 'Periods to set' }}
                                                    · {{ $copy['offering']->capacity ? 'Capacity '.$copy['offering']->capacity : 'No capacity' }}
                                                    · {{ $copy['section_count'] }} {{ $copy['section_count'] === 1 ? 'section' : 'sections' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($skips->isNotEmpty())
                        <div class="rounded-md border border-amber-500/30 bg-amber-500/10 p-4 text-sm">
                            <p class="font-semibold">{{ $skips->count() }} already exist in {{ $target->name }}</p>
                            <p class="mt-1 text-muted-foreground">They will be left unchanged, so reviewing or confirming again will not create duplicates.</p>
                            <p class="mt-2">{{ $skips->map(fn ($offering) => $offering->subject->name.' · '.$offering->academicLevel->name.' · '.$offering->academicPeriod->display_name)->join('; ') }}</p>
                        </div>
                    @endif

                    @if ($problems->isNotEmpty())
                        <div class="rounded-md border border-destructive/30 bg-destructive/10 p-4 text-sm text-destructive">
                            <p class="font-semibold">Some subjects need attention first</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($problems as $problemItem)
                                    <li>{{ $problemItem['offering']->subject->name }} · {{ $problemItem['offering']->academicLevel->name }}: {{ $problemItem['reason'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($copies->isEmpty() && $skips->isEmpty() && $problems->isEmpty())
                        <p class="rounded-md border border-dashed p-4 text-sm text-muted-foreground">No subject offerings exist in {{ $source->name }} to roll over.</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-3 border-t pt-4">
                        @if ($copies->isNotEmpty())
                            <form method="POST" action="{{ route('course-offerings.roll-forward') }}">
                                @csrf
                                <input type="hidden" name="source_academic_year_id" value="{{ $source->id }}">
                                <input type="hidden" name="target_academic_year_id" value="{{ $target->id }}">
                                @if ($setupMode)
                                    <input type="hidden" name="setup" value="1">
                                @endif
                                <april:button type="submit">Create {{ $copies->count() }} {{ $copies->count() === 1 ? 'draft offering' : 'draft offerings' }}</april:button>
                            </form>
                        @endif
                        <april:button-link href="{{ $setupMode ? route('academic-years.setup', [$target, 'subjects']) : route('course-offerings.index') }}" variant="ghost">Cancel</april:button-link>
                    </div>
                </slot:content>
            </april:card>
        @endif
    </div>
@endsection
