@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Attendance register', 'active'],
]])

@section('title', 'Attendance register')
@section('page_heading', 'Attendance register')

@php
    $statuses = \App\Enums\AttendanceStatus::cases();
    $marked = $students->mapWithKeys(fn ($student) => [
        $student->id => ($records->get($student->id)?->status ?? \App\Enums\AttendanceStatus::Present)->value,
    ]);
    $previousDay = $date->copy()->subDay()->toDateString();
    $nextDay = $date->copy()->addDay()->toDateString();
    $linkFor = fn (string $day): string => route('attendance.register', [
        'academic_cycle_section_id' => $section?->id,
        'attended_on' => $day,
    ]);
@endphp

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <april:alert>
                <slot:title>Saved</slot:title>
                <slot:description>{{ session('success') }}</slot:description>
            </april:alert>
        @endif

        <x-display-validation-errors />

        <april:card>
            <slot:title>Which register?</slot:title>
            <slot:description>Choose a {{ school_term('section', 'home section') }} and a day. The register opens on today.</slot:description>
            <slot:content>
                <form method="GET" class="grid gap-4 md:grid-cols-[2fr_1fr_auto] md:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="register-section">{{ school_term('section', 'Home section') }}</april:label>
                        <april:native-select id="register-section" name="academic_cycle_section_id">
                            <option value="">Choose a {{ school_term('section', 'home section') }}</option>
                            @foreach ($sections as $item)
                                <option value="{{ $item->id }}" @selected($section?->id === $item->id)>
                                    {{ $item->academicLevel?->label ?? $item->academicLevel?->name }} · {{ $item->label ?? $item->name }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="register-date">Day</april:label>
                        <input id="register-date" name="attended_on" type="date" value="{{ $date->toDateString() }}"
                            class="h-10 rounded-md border border-input bg-background px-3 text-sm" />
                    </div>

                    <april:button type="submit">
                        <x-lucide-search class="mr-2 size-4" />
                        Open register
                    </april:button>
                </form>

                @if ($section)
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t pt-4">
                        <april:button-link href="{{ $linkFor($previousDay) }}" variant="outline">
                            <x-lucide-chevron-left class="mr-1 size-4" />
                            Day before
                        </april:button-link>
                        <april:button-link href="{{ $linkFor(now()->toDateString()) }}" variant="outline">Today</april:button-link>
                        <april:button-link href="{{ $linkFor($nextDay) }}" variant="outline">
                            Day after
                            <x-lucide-chevron-right class="ml-1 size-4" />
                        </april:button-link>
                        <span class="ml-auto text-sm text-muted-foreground">{{ $date->format('l, j F Y') }}</span>
                    </div>
                @endif
            </slot:content>
        </april:card>

        @if ($section === null)
            <april:card>
                <slot:content>
                    <x-empty-state icon="lucide-clipboard-list" title="No register open"
                        description="Choose a {{ school_term('section', 'home section') }} above to mark who attended." />
                </slot:content>
            </april:card>
        @elseif ($students->isEmpty())
            <april:card>
                <slot:title>{{ $section->academicLevel?->label ?? $section->academicLevel?->name }} · {{ $section->label ?? $section->name }}</slot:title>
                <slot:content>
                    <x-empty-state icon="lucide-users" title="Nobody attends this {{ school_term('section', 'home section') }} yet"
                        description="Place a learner here first, then the register will list them.">
                        <april:button-link href="{{ route('students.index') }}">Go to students</april:button-link>
                    </x-empty-state>
                </slot:content>
            </april:card>
        @else
            <form method="POST" action="{{ route('attendance.register.store') }}"
                x-data="{
                    marked: {{ Js::from($marked) }},
                    count(value) { return Object.values(this.marked).filter((status) => status === value).length },
                    setAll(value) { Object.keys(this.marked).forEach((key) => this.marked[key] = value) },
                }">
                @csrf
                <input type="hidden" name="academic_cycle_section_id" value="{{ $section->id }}">
                <input type="hidden" name="attended_on" value="{{ $date->toDateString() }}">

                <april:card>
                    <slot:title>{{ $section->academicLevel?->label ?? $section->academicLevel?->name }} · {{ $section->label ?? $section->name }}</slot:title>
                    <slot:description>{{ $students->count() }} {{ Str::plural('learner', $students->count()) }} on {{ $date->format('j F Y') }}.</slot:description>
                    <slot:content>
                        <div class="flex flex-wrap items-center gap-2 pb-4">
                            <april:badge variant="secondary">Present <span class="ml-1 font-bold" x-text="count('present')"></span></april:badge>
                            <april:badge variant="destructive">Absent <span class="ml-1 font-bold" x-text="count('absent')"></span></april:badge>
                            <april:badge variant="outline">Late <span class="ml-1 font-bold" x-text="count('late')"></span></april:badge>
                            <april:badge variant="outline">Excused <span class="ml-1 font-bold" x-text="count('excused')"></span></april:badge>

                            <div class="ml-auto flex flex-wrap gap-2">
                                <april:button type="button" variant="outline" x-on:click="setAll('present')">Mark everybody present</april:button>
                                <april:button type="button" variant="outline" x-on:click="setAll('absent')">Mark everybody absent</april:button>
                            </div>
                        </div>

                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Learner</april:data-table-head>
                                    <april:data-table-head>Admission number</april:data-table-head>
                                    <april:data-table-head class="w-56">Attendance</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($students as $student)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $student->user?->name ?? 'Unnamed learner' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">
                                            {{ $student->admission_number ?? '—' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell>
                                            <april:native-select
                                                name="statuses[{{ $student->id }}]"
                                                aria-label="Attendance for {{ $student->user?->name ?? $student->admission_number }}"
                                                class="w-full"
                                                x-model="marked[{{ $student->id }}]">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                @endforeach
                                            </april:native-select>
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    </slot:content>
                    <slot:footer>
                        <april:button type="submit">
                            <x-lucide-save class="mr-2 size-4" />
                            Save register
                        </april:button>
                    </slot:footer>
                </april:card>
            </form>
        @endif
    </div>
@endsection
