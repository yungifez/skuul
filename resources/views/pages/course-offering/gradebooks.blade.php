@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('gradebooks.index'), 'text' => 'Gradebooks', 'active'],
]])

@section('title', 'Gradebooks')
@section('page_heading', 'Gradebooks')

@section('content')
    <april:card>
        <slot:title>Gradebooks for {{ $academicPeriod?->displayName ?? $academicYear->name }}</slot:title>
        <slot:description>Open a subject gradebook for {{ $academicYear->name }}. Closed periods remain available as read-only history.</slot:description>
        <slot:content>
            <form method="GET" action="{{ route('gradebooks.index') }}" class="mb-6 grid gap-4 rounded-lg border bg-muted/20 p-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
                <div>
                    <april:label for="gradebook-academic-year">Academic year</april:label>
                    <april:native-select id="gradebook-academic-year" name="academic_year_id" required>
                        @foreach ($academicYears as $availableAcademicYear)
                            <option value="{{ $availableAcademicYear->id }}" @selected($availableAcademicYear->id === $selectedAcademicYearId)>{{ $availableAcademicYear->name }}</option>
                        @endforeach
                    </april:native-select>
                </div>
                <div>
                    <april:label for="gradebook-academic-period">Period</april:label>
                    <april:native-select id="gradebook-academic-period" name="academic_period_id">
                        <option value="">{{ $selectedAcademicPeriodId === null ? 'All periods' : 'Working period' }}</option>
                        @foreach ($academicYear->topLevelPeriods as $availableAcademicPeriod)
                            <option value="{{ $availableAcademicPeriod->id }}" @selected($availableAcademicPeriod->id === $selectedAcademicPeriodId)>{{ $availableAcademicPeriod->displayName }} · {{ $availableAcademicPeriod->status->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>
                <april:button type="submit">View gradebooks</april:button>
            </form>

            @if ($courseOfferings->isEmpty())
                <div class="space-y-3 py-6 text-center">
                    <p class="font-medium">No gradebooks are available for this selection.</p>
                    <p class="text-sm text-muted-foreground">Choose another year or period, or add subjects to this year first.</p>
                    <april:button-link href="{{ route('course-offerings.index') }}" variant="outline">View subjects being taught</april:button-link>
                </div>
            @else
                <div class="grid gap-2 md:hidden" role="note">
                    <p class="text-xs text-muted-foreground">Swipe horizontally to view all gradebook columns.</p>
                </div>
                <div class="overflow-x-auto rounded-md border" role="region" aria-label="Gradebook list" tabindex="0">
                    <table class="w-full min-w-[760px] align-middle text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2">Subject</th>
                                <th class="px-3 py-2">Class</th>
                                <th class="px-3 py-2">Period</th>
                                <th class="px-3 py-2">Sections</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($courseOfferings as $courseOffering)
                                <tr>
                                    <td class="px-3 py-3 font-medium">
                                        {{ $courseOffering->subject->name }}
                                        @if ($courseOffering->subject->short_name)
                                            <span class="ml-1 text-muted-foreground">{{ $courseOffering->subject->short_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $courseOffering->academicLevel->name }}</td>
                                    <td class="px-3 py-3">
                                        <span class="block">{{ $courseOffering->academicPeriod->displayName }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $courseOffering->academicPeriod->status->label() }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-muted-foreground">
                                        {{ $courseOffering->cycleSections->isEmpty() ? school_roster_label($courseOffering->roster_mode) : $courseOffering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', ') }}
                                    </td>
                                    <td class="px-3 py-3"><april:badge>{{ $courseOffering->status->label() }}</april:badge></td>
                                    <td class="px-3 py-3 text-right">
                                        <april:button-link href="{{ route('course-offerings.gradebook.show', $courseOffering) }}" variant="outline" size="sm">Open gradebook</april:button-link>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $courseOfferings->links('components.pagination-links-view') }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
