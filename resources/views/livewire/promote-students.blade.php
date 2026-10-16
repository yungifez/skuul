@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $sectionWord = strtolower(school_term('section', 'section'));
@endphp
<div class="space-y-6">
    <form wire:submit="loadStudents" class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="promotion-source" class="text-sm text-muted-foreground">Current {{ $sectionWord }}</label>
            <select id="promotion-source" wire:model.live="sourceAcademicCycleSectionId" class="{{ $controlClasses }}" {{ field_error_bindings('sourceAcademicCycleSectionId') }}>
                <option value="">Choose the current {{ $sectionWord }}</option>
                @foreach ($cycleSections as $cycleSection)
                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                @endforeach
            </select>
            <x-field-error name="sourceAcademicCycleSectionId" class="mt-1" />
        </div>
        <div>
            <label for="promotion-destination" class="text-sm text-muted-foreground">Destination {{ $sectionWord }}</label>
            <select id="promotion-destination" wire:model.live="destinationAcademicCycleSectionId" class="{{ $controlClasses }}" {{ field_error_bindings('destinationAcademicCycleSectionId') }}>
                <option value="">Choose the destination {{ $sectionWord }}</option>
                @foreach ($cycleSections as $cycleSection)
                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                @endforeach
            </select>
            <x-field-error name="destinationAcademicCycleSectionId" class="mt-1" />
        </div>
        <div class="flex items-end">
            <april:button type="submit" variant="outline" class="h-11 w-full select-none md:w-auto" wire:loading.attr="disabled" wire:target="loadStudents">Review learners</april:button>
        </div>
    </form>

    @if ($students !== [])
        <form wire:submit="promote" class="space-y-4">
            <h2 class="text-base font-semibold">Learners to move</h2>
            <ul class="divide-y border-y">
                @foreach ($students as $student)
                    <li wire:key="promotion-student-{{ $student['id'] }}">
                        <label class="flex min-h-11 select-none items-center gap-3 py-2 text-sm">
                            <input type="checkbox" wire:model="selectedStudentIds" value="{{ $student['id'] }}" class="size-4 rounded border-input">
                            <span class="min-w-0 flex-1 truncate font-medium">{{ $student['name'] }}</span>
                            <span class="shrink-0 text-muted-foreground">{{ $student['admission_number'] ?? '—' }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <x-field-error name="selectedStudentIds" />
            <p class="text-sm text-muted-foreground">Each move stays in the learner's placement history.</p>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="promote" wire:confirm="Move the selected learners now?">Move selected learners</april:button>
        </form>
    @endif
</div>
