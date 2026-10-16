@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $copies = $preview['copies'] ?? collect();
    $skips = $preview['skips'] ?? collect();
    $problems = $preview['problems'] ?? collect();
@endphp
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">Each subject becomes a draft in the new year with its periods per week, capacity, and matching sections. Learners, teachers, and marks stay behind.</p>

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
        @if ($copies->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-base font-semibold">Will be copied as drafts</h2>
                <ul class="divide-y border-y">
                    @foreach ($copies as $copy)
                        <li wire:key="copy-{{ $copy['offering']->id }}" class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                            <span class="min-w-0 flex-1 font-medium">{{ $copy['offering']->subject->name }} · {{ $copy['offering']->academicLevel->name }}</span>
                            <span class="text-muted-foreground">{{ $copy['period']->display_name }} · {{ school_roster_label($copy['offering']->roster_mode) }} · {{ $copy['offering']->planned_periods_per_week ? $copy['offering']->planned_periods_per_week.' a week' : '—' }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($skips->isNotEmpty())
            <section class="space-y-1 text-sm">
                <h2 class="text-base font-semibold">Already in {{ $target->name }}</h2>
                <p class="text-muted-foreground">{{ $skips->map(fn ($offering) => $offering->subject->name.' · '.$offering->academicLevel->name)->join('; ') }}</p>
            </section>
        @endif

        @if ($problems->isNotEmpty())
            <section class="space-y-2 text-sm">
                <h2 class="text-base font-semibold">Need attention first</h2>
                <ul class="divide-y border-y">
                    @foreach ($problems as $problemItem)
                        <li class="py-3"><span class="font-medium">{{ $problemItem['offering']->subject->name }} · {{ $problemItem['offering']->academicLevel->name }}</span> <span class="text-muted-foreground">{{ $problemItem['reason'] }}</span></li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($copies->isEmpty() && $skips->isEmpty() && $problems->isEmpty())
            <p class="text-sm text-muted-foreground">{{ $source->name }} has no subjects to copy.</p>
        @endif

        @if ($copies->isNotEmpty())
            <div>
                <april:button type="button" class="h-11 select-none" wire:click="rollOver" wire:loading.attr="disabled" wire:target="rollOver">Copy {{ $copies->count() }} {{ $copies->count() === 1 ? 'subject' : 'subjects' }}</april:button>
            </div>
        @endif
    @endif
</div>
