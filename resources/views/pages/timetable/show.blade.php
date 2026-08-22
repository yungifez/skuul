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
            Print timetable
        </april:button>
    </div>
    @can('update', $timetable)
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
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection
