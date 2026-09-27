@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $sectionWord = strtolower(school_term('section', 'section'));
@endphp
<div class="space-y-6">
    <form wire:submit="loadStudents" class="grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label for="graduation-section" class="text-sm text-muted-foreground">{{ school_term('section', 'Section') }}</label>
            <select id="graduation-section" wire:model.live="academicCycleSectionId" class="{{ $controlClasses }}" {{ field_error_bindings('academicCycleSectionId') }}>
                <option value="">Choose a {{ $sectionWord }}</option>
                @foreach ($cycleSections as $cycleSection)
                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                @endforeach
            </select>
            <x-field-error name="academicCycleSectionId" class="mt-1" />
        </div>
        <div class="flex items-end">
            <april:button type="submit" variant="outline" class="h-11 w-full select-none md:w-auto" wire:loading.attr="disabled" wire:target="loadStudents">Review learners</april:button>
        </div>
    </form>

    @if ($students !== [])
        <form wire:submit="graduate" class="space-y-4">
            <h2 class="text-base font-semibold">Learners to graduate</h2>
            <ul class="divide-y border-y">
                @foreach ($students as $student)
                    <li wire:key="graduation-student-{{ $student['id'] }}">
                        <label class="flex min-h-11 select-none items-center gap-3 py-2 text-sm">
                            <input type="checkbox" wire:model="selectedStudentIds" value="{{ $student['id'] }}" class="size-5 rounded border-input">
                            <span class="min-w-0 flex-1 truncate font-medium">{{ $student['name'] }}</span>
                            <span class="shrink-0 text-muted-foreground">{{ $student['admission_number'] ?? '—' }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <x-field-error name="selectedStudentIds" />
            <div>
                <label for="graduation-reason" class="text-sm text-muted-foreground">Note for the record (optional)</label>
                <input id="graduation-reason" type="text" maxlength="255" wire:model="reason" placeholder="Completed the final year" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                <x-field-error name="reason" class="mt-1" />
            </div>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="graduate" wire:confirm="Graduate the selected learners now? They leave the student lists.">Graduate selected learners</april:button>
        </form>
    @endif
</div>
