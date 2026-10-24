@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Timetable', 'active'],
]])

@section('title', 'Timetable')
@section('page_heading', 'Timetable')

@section('content')
    <div class="flex w-full flex-col gap-5">
        <p class="text-sm text-muted-foreground">
            The week of {{ $studentRecord->user?->name }}, with the lessons they take · {{ $studentRecord->school?->name }}.
        </p>

        <april:card>
            <slot:title>This week</slot:title>
            @if ($timetables->isNotEmpty())
                <slot:description>From {{ $timetables->pluck('name')->join(', ', ' and ') }}</slot:description>
            @endif
            <slot:content>
                @if ($days === [])
                    <x-empty-state icon="lucide-clock" title="No timetable yet"
                        description="The timetable appears here when the school publishes it." />
                @else
                    <x-personal-week :days="$days" />
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
