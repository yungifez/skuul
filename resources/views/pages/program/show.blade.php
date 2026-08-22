@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('programs.index'), 'text' => 'Programmes'],
    ['text' => $program->name, 'active'],
]])

@section('title', $program->name)
@section('page_heading', $program->name)

@section('page_actions')
    <april:button-link href="{{ route('programs.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to programmes
    </april:button-link>
@endsection

@php
    $running = $program->participations->filter(fn ($place) => $place->status->isRunning());
    $canWrite = auth()->user()->can('update', $program);
@endphp

@section('content')
    <div class="space-y-6">
        @if ($errors->has('participation'))
            <april:alert variant="destructive">
                <slot:title>The place did not change</slot:title>
                <slot:description>{{ $errors->first('participation') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $program->type->label() }}</slot:title>
            <slot:description>{{ $program->description ?? 'This programme has no description.' }}</slot:description>
            <slot:content>
                <dl class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">Taking part now</dt>
                        <dd class="text-2xl font-semibold">{{ $running->count() }}</dd>
                    </div>
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">Places ever given</dt>
                        <dd class="text-2xl font-semibold">{{ $program->participations->count() }}</dd>
                    </div>
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">State</dt>
                        <dd class="text-lg font-semibold">{{ $program->is_active ? 'Open' : 'Closed' }}</dd>
                        <p class="mt-1 text-xs text-muted-foreground">A closed programme takes no new places</p>
                    </div>
                </dl>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Places</slot:title>
            <slot:description>A place moves through its own states and never changes the learner's enrollment.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($program->participations->isEmpty())
                        <x-empty-state icon="lucide-user-plus" title="Nobody takes part yet"
                            description="Give a learner a place below." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Learner</april:data-table-head>
                                    <april:data-table-head>When</april:data-table-head>
                                    <april:data-table-head>Run by</april:data-table-head>
                                    <april:data-table-head>State</april:data-table-head>
                                    <april:data-table-head class="text-right">Move it</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($program->participations as $place)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $place->studentRecord->user?->name ?? $place->studentRecord->admission_number }}
                                            <span class="block text-xs text-muted-foreground">{{ $place->studentRecord->admission_number }}</span>
                                        </april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $place->starts_on?->format('j M Y') ?? 'No date' }}
                                            @if (filled($place->schedule))
                                                <span class="block text-xs">{{ $place->schedule }}</span>
                                            @endif
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">{{ $place->staff?->name ?? 'Nobody named' }}</april:data-table-cell>
                                        <april:data-table-cell>
                                            <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                {{ $place->status->label() }}
                                            </span>
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            @if ($canWrite && $place->status->allowedNext() !== [])
                                                <form method="POST" action="{{ route('programs.participations.update', [$program, $place]) }}"
                                                    class="flex items-end justify-end gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <april:native-select name="status" aria-label="Move the place to" class="w-auto">
                                                        @foreach ($place->status->allowedNext() as $status)
                                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                        @endforeach
                                                    </april:native-select>
                                                    <april:button type="submit" variant="outline" size="sm">Move</april:button>
                                                </form>
                                            @else
                                                <span class="text-sm text-muted-foreground">Finished</span>
                                            @endif
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('programs.participations.store', $program) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-5 lg:items-end">
                            @csrf

                            <div class="flex flex-col gap-2 lg:col-span-2">
                                <april:label for="student_record_id">Learner</april:label>
                                <april:native-select id="student_record_id" name="student_record_id" required>
                                    <option value="">Choose a learner</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                                @error('student_record_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="starts_on">Starts on</april:label>
                                <input type="date" id="starts_on" name="starts_on" value="{{ old('starts_on', now()->toDateString()) }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="schedule">When it runs</april:label>
                                <april:input id="schedule" name="schedule" value="{{ old('schedule') }}" placeholder="Tuesday, 15:30" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="staff_id">Run by</april:label>
                                <april:native-select id="staff_id" name="staff_id">
                                    <option value="">Nobody yet</option>
                                    @foreach ($staff as $person)
                                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <april:button type="submit" class="lg:col-span-5 lg:justify-self-start">
                                <x-lucide-user-plus class="mr-2 size-4" />
                                Give a place
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>
    </div>
@endsection
