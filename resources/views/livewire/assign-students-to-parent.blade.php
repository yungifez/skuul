<div class="flex max-w-3xl flex-col gap-6">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-60';
    @endphp

    <form wire:submit="add" class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] sm:items-end" aria-label="Link a learner">
        <div>
            <label for="academic-cycle-section" class="text-sm text-muted-foreground">{{ school_term('section', 'Section') }}</label>
            <select id="academic-cycle-section" wire:model.live="academicCycleSectionId" class="{{ $controlClasses }}" {{ field_error_bindings('academicCycleSectionId') }}>
                @forelse ($cycleSections as $cycleSection)
                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                @empty
                    <option value="">No active {{ strtolower(school_terms('section', 'sections')) }} this {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                @endforelse
            </select>
            <x-field-error name="academicCycleSectionId" class="mt-1" />
        </div>
        <div>
            <label for="student" class="text-sm text-muted-foreground">Learner</label>
            <select id="student" wire:model="studentId" @disabled($students === []) class="{{ $controlClasses }}" {{ field_error_bindings('studentId') }}>
                @forelse ($students as $student)
                    <option value="{{ $student['id'] }}">{{ $student['name'] }}@if ($student['admission_number']) · {{ $student['admission_number'] }}@endif</option>
                @empty
                    <option value="">No learner left to link in this {{ strtolower(school_term('section', 'section')) }}</option>
                @endforelse
            </select>
            <x-field-error name="studentId" class="mt-1" />
        </div>
        <april:button type="submit" class="h-11 select-none" :disabled="$studentId === null" wire:loading.attr="disabled" wire:target="add">Link learner</april:button>
    </form>

    <section aria-labelledby="linked-learners-heading">
        <h2 id="linked-learners-heading" class="text-base font-semibold">Linked learners</h2>
        <p class="text-sm text-muted-foreground">They can read these learners' records in the portal. Linking does not change a learner's class.</p>

        @if ($children === [])
            <p class="mt-3 border-y py-4 text-sm text-muted-foreground">No learner at this school is linked yet.</p>
        @else
            <ul class="mt-3 divide-y border-y">
                @foreach ($children as $student)
                    <li wire:key="child-{{ $student['id'] }}" class="flex min-h-11 items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $student['name'] }}</p>
                            <p class="truncate text-sm text-muted-foreground">{{ $student['cycle_section'] ?? 'Not placed' }} · {{ $student['admission_number'] ?? '—' }}</p>
                        </div>
                        <april:button type="button" variant="outline" class="h-11 shrink-0 select-none"
                            wire:click="remove({{ $student['id'] }})"
                            wire:confirm="Unlink {{ $student['name'] }}? {{ $parent->name }} will no longer see their records."
                            wire:loading.attr="disabled" wire:target="remove">
                            Unlink
                        </april:button>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
