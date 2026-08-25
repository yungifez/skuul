@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'timetables'],
    ['href'=> route('timetables.show', $timetable->id), 'text'=> "View $timetable->name", 'active'],
]])

@section('title', __("View $timetable->name"))

@section('page_heading', __("View $timetable->name") )

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <x-timetable-status-control :timetable="$timetable" />
        <april:button variant="outline" type="button" onclick="window.location='{{ route('timetables.print', $timetable->id) }}'">
            <x-lucide-printer class="mr-2 size-4" />
            Open print view
        </april:button>
    </div>
    @can('override', $timetable)
        @if ($timetable->status === \App\Enums\TimetableStatus::Published && $overrideSections->isNotEmpty())
            <form method="POST" action="{{ route('timetables.section-overrides.store', $timetable) }}" class="mb-4 flex flex-wrap items-end gap-3 rounded-md border p-4">
                @csrf
                <label class="flex flex-col gap-1 text-sm font-medium">Create an override for
                    <select name="academic_cycle_section_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                        @foreach ($overrideSections as $section)
                            <option value="{{ $section->id }}">{{ $section->label ?? $section->name }}</option>
                        @endforeach
                    </select>
                </label>
                <april:button type="submit">Create override draft</april:button>
            </form>
        @endif
    @endcan
    @can('substitute', $timetable)
        @if ($timetable->status === \App\Enums\TimetableStatus::Published && $substitutionEntries->isNotEmpty())
            <section class="mb-4 rounded-md border p-4" aria-labelledby="substitution-heading">
                <div class="mb-3">
                    <h2 id="substitution-heading" class="font-semibold">Cover a scheduled lesson</h2>
                    <p class="text-sm text-muted-foreground">This records who is covering one date. It does not alter the published weekly timetable.</p>
                </div>
                @if ($replacementTeachers->isNotEmpty())
                    <form method="POST" action="{{ route('timetables.substitutions.store', $timetable) }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        @csrf
                        <label class="flex flex-col gap-1 text-sm font-medium">Scheduled lesson
                            <select name="timetable_entry" class="rounded-md border border-input bg-background px-3 py-2" required>
                                @foreach ($substitutionEntries as $entry)
                                    <option value="{{ $entry->timetable_time_slot_id }}:{{ $entry->weekday_id }}">{{ $entry->weekday_name }} · {{ $entry->start_time }}–{{ $entry->stop_time }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="flex flex-col gap-1 text-sm font-medium">Covering teacher
                            <select name="replacement_teacher_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                                @foreach ($replacementTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="flex flex-col gap-1 text-sm font-medium">Date
                            <input type="date" name="substituted_on" class="rounded-md border border-input bg-background px-3 py-2" required>
                        </label>
                        <label class="flex flex-col gap-1 text-sm font-medium">Reason
                            <input type="text" name="reason" maxlength="1000" class="rounded-md border border-input bg-background px-3 py-2" placeholder="e.g. Staff absence" required>
                        </label>
                        <div class="md:col-span-2 xl:col-span-4">
                            <april:button type="submit">Record cover</april:button>
                        </div>
                    </form>
                @else
                    <p class="text-sm text-muted-foreground">Add an active teacher to this school before recording cover.</p>
                @endif
            </section>
        @endif
    @endcan
    @if ($substitutions->isNotEmpty())
        <section class="mb-4 rounded-md border p-4" aria-labelledby="substitutions-heading">
            <h2 id="substitutions-heading" class="mb-3 font-semibold">Recorded cover</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-2 py-2 font-medium">Date</th>
                            <th class="px-2 py-2 font-medium">Scheduled lesson</th>
                            <th class="px-2 py-2 font-medium">Covering teacher</th>
                            <th class="px-2 py-2 font-medium">Reason</th>
                            <th class="px-2 py-2 font-medium">Approved by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($substitutions as $substitution)
                            <tr class="border-b last:border-0">
                                <td class="px-2 py-2">{{ $substitution->substituted_on->format('j M Y') }}</td>
                                <td class="px-2 py-2">{{ $substitution->weekday->name }} · {{ $substitution->timeSlot->start_time }}–{{ $substitution->timeSlot->stop_time }}</td>
                                <td class="px-2 py-2">{{ $substitution->replacementTeacher->name }}</td>
                                <td class="px-2 py-2">{{ $substitution->reason }}</td>
                                <td class="px-2 py-2">{{ $substitution->approvedBy->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection
