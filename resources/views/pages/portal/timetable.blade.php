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
            @if ($timetable !== null)
                <slot:title>{{ $timetable->name }}</slot:title>
            @endif
            <slot:content>
                @if ($timetable === null || $grid['rows'] === [])
                    <x-empty-state icon="lucide-clock" title="No timetable yet"
                        description="The timetable appears here when the school publishes it." />
                @else
                    @include('livewire.partials.timetable-grid', ['grid' => $grid])
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
