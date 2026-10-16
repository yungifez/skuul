@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $copies = $preview['copies'] ?? collect();
    $skips = $preview['skips'] ?? collect();
    $leftBehind = $preview['leftBehind'] ?? collect();
    $sectionWord = strtolower(school_term('section', 'section'));
    $sectionsWord = strtolower(school_terms('section', 'Section'));
    $targetIsClosed = $target?->isClosed() ?? false;
@endphp
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">Each {{ $sectionWord }} becomes a draft in the new year with its name, stream, shift, language, room, and capacity. Learners, {{ strtolower(school_terms('homeroom_teacher', 'Class teacher')) }}, attendance, results, and timetables stay behind.</p>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="source-year" class="text-sm text-muted-foreground">Copy from</label>
            <select id="source-year" wire:model.live="sourceAcademicYearId" class="{{ $controlClasses }}" {{ field_error_bindings('sourceAcademicYearId') }}>
                <option value="">Choose a school year</option>
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>
            <x-field-error name="sourceAcademicYearId" class="mt-1" />
        </div>
        <div>
            <label for="target-year" class="text-sm text-muted-foreground">Create in</label>
            <select id="target-year" wire:model.live="targetAcademicYearId" class="{{ $controlClasses }}" {{ field_error_bindings('targetAcademicYearId') }}>
                <option value="">Choose a school year</option>
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>
            <x-field-error name="targetAcademicYearId" class="mt-1" />
        </div>
    </div>

    @if ($problem)
        <p class="text-sm text-destructive" role="alert">{{ $problem }}</p>
    @elseif ($preview !== null)
        @if ($targetIsClosed)
            <p class="text-sm text-destructive" role="alert">{{ $target->name }} is closed. Reopen it before you copy {{ $sectionsWord }} into it.</p>
        @endif

        @if ($copies->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-base font-semibold">Will be created as drafts</h2>
                <ul class="divide-y border-y">
                    @foreach ($copies as $copy)
                        <li wire:key="copy-{{ $copy->id }}" class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                            <span class="min-w-0 flex-1 font-medium">{{ $copy->academicLevel->name }} · {{ $copy->label ?? $copy->name }}</span>
                            <span class="text-muted-foreground">{{ collect([$copy->stream, $copy->shift, $copy->room])->filter()->join(' · ') ?: '—' }} · <span class="tabular-nums">{{ $copy->capacity ?? '—' }}</span> places</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($skips->isNotEmpty())
            <section class="space-y-1 text-sm">
                <h2 class="text-base font-semibold">Already in {{ $target->name }}</h2>
                <p class="text-muted-foreground">{{ $skips->map(fn ($skip) => $skip->academicLevel->name.' · '.$skip->name)->join('; ') }}</p>
            </section>
        @endif

        @if ($leftBehind->isNotEmpty())
            <section class="space-y-1 text-sm">
                <h2 class="text-base font-semibold">Left behind</h2>
                <p class="text-muted-foreground">{{ $leftBehind->map(fn ($section) => $section->academicLevel->name.' · '.$section->name)->join('; ') }}. The {{ $sectionWord }} is archived, or its {{ strtolower(school_term('class_level', 'class')) }} is archived or now holds other {{ strtolower(school_terms('class_level', 'Class')) }}.</p>
            </section>
        @endif

        @if ($copies->isEmpty() && $skips->isEmpty() && $leftBehind->isEmpty())
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <p class="text-muted-foreground">{{ $source->name }} has no {{ $sectionsWord }} to copy.</p>
                <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $source->id] + ($setup ? ['setup' => 1, 'school_setup' => 1] : [])) }}" variant="outline" class="h-11 select-none">Add a {{ $sectionWord }} to {{ $source->name }}</april:button-link>
            </div>
        @elseif ($copies->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing left to copy into {{ $target->name }}.</p>
        @endif

        @if ($copies->isNotEmpty() && ! $targetIsClosed)
            <div>
                <april:button type="button" class="h-11 select-none" wire:click="rollForward" wire:loading.attr="disabled" wire:target="rollForward">Create {{ $copies->count() }} draft {{ $copies->count() === 1 ? $sectionWord : $sectionsWord }}</april:button>
            </div>
        @endif
    @endif
</div>
