@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['href' => route('academic-cycle-sections.roll-forward.show'), 'text' => 'Roll forward', 'active'],
]])

@section('title', __('Roll '.strtolower(school_terms('section', 'sections')).' into another year'))
@section('page_heading', __('Roll '.strtolower(school_terms('section', 'sections')).' into another year'))

@section('content')
    @php
        $targetIsClosed = $target?->isClosed() ?? false;
        $copies = $preview['copies'] ?? null;
        $skips = $preview['skips'] ?? null;
        $setupMode = request()->boolean('setup');
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <april:card class="lg:col-span-2">
            <slot:title>1. Choose the two cycles</slot:title>
            <slot:description>The structure is read from one cycle and created again in the other. Nothing is written until you confirm.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('academic-cycle-sections.roll-forward.show') }}" class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
                    @if ($setupMode)
                        <input type="hidden" name="setup" value="1">
                    @endif
                    <div class="flex flex-col gap-1.5">
                        <april:label for="source-cycle">Copy the structure of</april:label>
                        <select id="source-cycle" name="source_academic_year_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                            <option value="">Select a cycle</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ $source?->id === $academicYear->id ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <april:label for="target-cycle">Into</april:label>
                        <select id="target-cycle" name="target_academic_year_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                            <option value="">Select a cycle</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ $target?->id === $academicYear->id ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <april:button type="submit" variant="outline">Review the copy</april:button>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What a roll forward does</slot:title>
            <slot:content class="space-y-4 text-sm">
                <div>
                    <p class="mb-1 font-medium">It copies the structure only</p>
                    <ul class="list-inside list-disc space-y-0.5 text-muted-foreground">
                        <li>{{ school_term('section', 'Section') }} name and local label</li>
                        <li>{{ school_term('class_level', 'Class') }}</li>
                        <li>Stream, shift, and language</li>
                        <li>Room, capacity, and display order</li>
                    </ul>
                </div>
                <div>
                    <p class="mb-1 font-medium">It never copies</p>
                    <ul class="list-inside list-disc space-y-0.5 text-muted-foreground">
                        <li>Learners and their placements</li>
                        <li>{{ school_term('homeroom_teacher', 'Class teacher') }} and teaching assignments</li>
                        <li>Attendance records</li>
                        <li>Grades, results, and reports</li>
                        <li>Timetable entries</li>
                    </ul>
                </div>
                <p class="text-muted-foreground">Every new section arrives as a draft, so nothing goes live until someone activates it.</p>
            </slot:content>
        </april:card>
    </div>

    @if ($problem)
        <april:alert variant="destructive" class="mt-6">
            <slot:title>That pair of cycles does not work</slot:title>
            <slot:description>{{ $problem }}</slot:description>
        </april:alert>
    @elseif ($preview !== null)
        <april:card class="mt-6">
            <slot:title>2. Review what will be created</slot:title>
            <slot:description>{{ $source->name }} → {{ $target->name }}</slot:description>
            <slot:content class="space-y-6">
                @if ($targetIsClosed)
                    <april:alert variant="destructive">
                        <slot:title>{{ $target->name }} is closed</slot:title>
                        <slot:description>Reopen the {{ strtolower(school_term('academic_year', 'school year')) }} before you copy a structure into it.</slot:description>
                    </april:alert>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-md border p-4">
                        <p class="text-2xl font-semibold">{{ $copies->count() }}</p>
                        <p class="text-sm text-muted-foreground">draft sections would be created in {{ $target->name }}</p>
                    </div>
                    <div class="rounded-md border p-4">
                        <p class="text-2xl font-semibold">{{ $skips->count() }}</p>
                        <p class="text-sm text-muted-foreground">would be skipped because {{ $target->name }} already has that name in the {{ strtolower(school_term('class_level', 'class')) }}</p>
                    </div>
                </div>

                @if ($copies->isEmpty() && $skips->isEmpty())
                        <x-empty-state
                        icon="lucide-copy"
                        title="{{ $source->name }} has no {{ strtolower(school_term('section', 'section')) }} to copy"
                        description="Create the structure in the source cycle first, or choose another cycle to copy from.">
                        <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $source->id] + ($setupMode ? ['setup' => 1, 'school_setup' => 1] : [])) }}" variant="outline" size="sm">Add a {{ strtolower(school_term('section', 'section')) }} to {{ $source->name }}</april:button-link>
                    </x-empty-state>
                @else
                    @if ($copies->isNotEmpty())
                        <div>
                            <h3 class="mb-2 text-sm font-semibold">Will be created as drafts</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[620px] text-sm">
                                    <thead class="border-b text-left text-muted-foreground">
                                        <tr>
                                            <th class="px-3 py-2">Level</th>
                                            <th class="px-3 py-2">Section</th>
                                            <th class="px-3 py-2">Stream / shift</th>
                                            <th class="px-3 py-2">Room</th>
                                            <th class="px-3 py-2">Capacity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach ($copies as $copy)
                                            <tr>
                                                <td class="px-3 py-2">{{ $copy->academicLevel->name }}</td>
                                                <td class="px-3 py-2 font-medium">{{ $copy->label ?? $copy->name }}</td>
                                                <td class="px-3 py-2">{{ collect([$copy->stream, $copy->shift])->filter()->join(' · ') ?: '—' }}</td>
                                                <td class="px-3 py-2">{{ $copy->room ?? '—' }}</td>
                                                <td class="px-3 py-2">{{ $copy->capacity ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($skips->isNotEmpty())
                        <div>
                            <h3 class="mb-2 text-sm font-semibold">Already in {{ $target->name }}, so they stay as they are</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($skips as $skip)
                                    <span class="rounded-md border px-3 py-1.5 text-sm text-muted-foreground">{{ $skip->academicLevel->name }} · {{ $skip->name }}</span>
                                @endforeach
                            </div>
                            <p class="mt-2 text-sm text-muted-foreground">This is why confirming twice is safe: a section is never created a second time.</p>
                        </div>
                    @endif

                    @if ($copies->isNotEmpty() && !$targetIsClosed)
                        <form method="POST" action="{{ route('academic-cycle-sections.roll-forward') }}" class="flex flex-wrap items-center gap-3 border-t pt-4">
                            @csrf
                            <input type="hidden" name="source_academic_year_id" value="{{ $source->id }}">
                            <input type="hidden" name="target_academic_year_id" value="{{ $target->id }}">
                            @if ($setupMode)
                                <input type="hidden" name="setup" value="1">
                            @endif
                            <april:button type="submit">
                                <x-lucide-copy class="mr-1.5 size-4" />
                                Create {{ $copies->count() }} {{ \Illuminate\Support\Str::plural('draft section', $copies->count()) }} in {{ $target->name }}
                            </april:button>
                            <april:button-link href="{{ $setupMode ? route('schools.setup', [current_school(), 'classes']) : route('academic-cycle-sections.index', ['academic_year_id' => $target->id]) }}" variant="ghost">Cancel</april:button-link>
                        </form>
                    @elseif ($copies->isEmpty())
                        <p class="border-t pt-4 text-sm text-muted-foreground">
                            {{ $target->name }} already has every section of {{ $source->name }}. There is nothing left to copy.
                        </p>
                    @endif
                @endif
            </slot:content>
        </april:card>
    @endif
@endsection
