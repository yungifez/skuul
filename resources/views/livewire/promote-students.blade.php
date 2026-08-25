<div class="space-y-6">
    <april:card>
        <slot:title>Move learners to a new {{ strtolower(school_term('section', 'section')) }}</slot:title>
        <slot:description>Choose the current and destination {{ strtolower(school_terms('section', 'sections')) }}, review the learners, then confirm the move. Each move remains in placement history.</slot:description>
        <slot:content>
            <form wire:submit="loadStudents" class="grid gap-4 md:grid-cols-3">
                <div class="flex flex-col gap-2">
                    <april:label for="promotion-source">Current {{ strtolower(school_term('section', 'section')) }}</april:label>
                    <select id="promotion-source" wire:model.live="sourceAcademicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Choose current section</option>
                        @foreach ($cycleSections as $cycleSection)
                            <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                        @endforeach
                    </select>
                    @error('sourceAcademicCycleSectionId') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-col gap-2">
                    <april:label for="promotion-destination">Destination {{ strtolower(school_term('section', 'section')) }}</april:label>
                    <select id="promotion-destination" wire:model.live="destinationAcademicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Choose destination section</option>
                        @foreach ($cycleSections as $cycleSection)
                            <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                        @endforeach
                    </select>
                    @error('destinationAcademicCycleSectionId') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <april:button type="submit" wire:loading.attr="disabled" wire:target="loadStudents">Review learners</april:button>
                </div>
            </form>
        </slot:content>
    </april:card>

    @if ($students !== [])
        <form action="{{ route('students.promote') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="source_academic_cycle_section_id" value="{{ $sourceAcademicCycleSectionId }}">
            <input type="hidden" name="destination_academic_cycle_section_id" value="{{ $destinationAcademicCycleSectionId }}">
            <april:card>
                <slot:title>Confirm learner move</slot:title>
                <slot:content>
                    <div class="space-y-3">
                        @foreach ($students as $student)
                            <label class="flex items-center gap-3 rounded-md border p-3">
                                <input type="checkbox" name="student_id[]" value="{{ $student['id'] }}" checked class="size-4 rounded border-input">
                                <span class="font-medium">{{ $student['name'] }}</span>
                                @if ($student['admission_number'])
                                    <span class="text-sm text-muted-foreground">{{ $student['admission_number'] }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </slot:content>
                <slot:footer><april:button type="submit">Move selected learners</april:button></slot:footer>
            </april:card>
        </form>
    @endif
</div>
