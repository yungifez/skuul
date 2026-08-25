<div class="space-y-6">
    <april:card>
        <slot:title>Graduate learners</slot:title>
        <slot:description>Choose a {{ strtolower(school_term('section', 'section')) }}, review the learners, then confirm. Each change stays in enrollment history.</slot:description>
        <slot:content>
            <form wire:submit="loadStudents" class="grid gap-4 md:grid-cols-3">
                <div class="flex flex-col gap-2 md:col-span-2">
                    <april:label for="graduation-section">{{ school_term('section', 'Section') }}</april:label>
                    <select id="graduation-section" wire:model.live="academicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Choose a {{ strtolower(school_term('section', 'section')) }}</option>
                        @foreach ($cycleSections as $cycleSection)
                            <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                        @endforeach
                    </select>
                    @error('academicCycleSectionId') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <april:button type="submit" wire:loading.attr="disabled" wire:target="loadStudents">Review learners</april:button>
                </div>
            </form>
        </slot:content>
    </april:card>

    @if ($students !== [])
        <form action="{{ route('students.graduate') }}" method="POST" class="space-y-4">
            @csrf
            <april:card>
                <slot:title>Confirm graduation</slot:title>
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
                <slot:footer><april:button type="submit">Graduate selected learners</april:button></slot:footer>
            </april:card>
        </form>
    @endif
</div>
