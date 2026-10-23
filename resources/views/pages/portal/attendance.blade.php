@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Attendance', 'active'],
]])

@section('title', 'Attendance')
@section('page_heading', 'Attendance')

@php
    $rate = $attendance['rate'];
    $recorded = $attendance['recorded'];
    // A campus that takes a register in each lesson counts lessons, not days.
    $byLesson = ($attendance['kind'] ?? 'daily') !== 'daily';
    $unit = $byLesson ? 'lesson' : 'day';
    $figures = [
        ['label' => 'Present', 'value' => $attendance['present'], 'hint' => $byLesson ? 'Lessons attended' : 'Days in school'],
        ['label' => 'Absent', 'value' => $attendance['absent'], 'hint' => $byLesson ? 'Lessons missed' : 'Days missed'],
        ['label' => 'Late', 'value' => $attendance['late'], 'hint' => $byLesson ? 'Lessons joined after the start' : 'Days of arriving after the start'],
        ['label' => 'Excused', 'value' => $attendance['excused'], 'hint' => 'Absences the school agreed to'],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }}</slot:title>
            <slot:description>The school counts only the {{ Str::plural($unit) }} it took a register. {{ ucfirst(with_indefinite_article($unit)) }} with no register does not count as an absence.</slot:description>
            <slot:content>
                @if ($recorded === 0)
                    <x-empty-state icon="lucide-calendar-off" title="No attendance recorded yet"
                        description="Once the school starts taking the register, the {{ Str::plural($unit) }} appear here." />
                @else
                    <div class="space-y-6">
                        <div>
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <p class="text-sm text-muted-foreground">Attendance rate</p>
                                <p class="text-sm text-muted-foreground">{{ $recorded }} {{ Str::plural($unit, $recorded) }} recorded</p>
                            </div>
                            <p class="text-4xl font-semibold">{{ $rate === null ? '—' : $rate.'%' }}</p>
                            @if ($rate !== null)
                                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-muted" role="img"
                                    aria-label="Attendance rate {{ $rate }} percent">
                                    <div class="h-full rounded-full bg-primary" style="width: {{ min($rate, 100) }}%"></div>
                                </div>
                            @endif
                        </div>

                        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($figures as $figure)
                                <div class="rounded-lg border p-4">
                                    <dt class="text-sm text-muted-foreground">{{ $figure['label'] }}</dt>
                                    <dd class="text-2xl font-semibold">{{ $figure['value'] }}</dd>
                                    <p class="mt-1 text-xs text-muted-foreground">{{ $figure['hint'] }}</p>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
