<div class="grid gap-4 md:grid-cols-12">
    <div class="md:col-span-12">
        <h4 class="text-xl font-bold md:text-2xl">Enrollment information</h4>
        <p class="mt-1 text-sm text-muted-foreground">Choose the student’s active {{ strtolower(school_term('section', 'section')) }} for the current academic year.</p>
    </div>
    <div class="flex w-full flex-col gap-2 md:col-span-4">
        <april:label for="academic-cycle-section-id">Choose a {{ strtolower(school_term('section', 'section')) }} *</april:label>
        <select id="academic-cycle-section-id" name="academic_cycle_section_id" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">Choose a {{ strtolower(school_term('section', 'section')) }}</option>
            @forelse ($cycleSections as $cycleSection)
                <option value="{{ $cycleSection['id'] }}" @selected(old('academic_cycle_section_id') == $cycleSection['id'])>{{ $cycleSection['level'] }} · {{ $cycleSection['name'] }}</option>
            @empty
                <option value="" disabled>No active sections are available for the current academic year</option>
            @endforelse
        </select>
        @error('academic_cycle_section_id')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <div class="md:col-span-4">
        <april:input-group id="admission-number" name="admission_number" label="Admission number" placeholder="Student's admission number" value="{{ old('admission_number') }}" />
    </div>
    <div class="md:col-span-4">
        <april:input-group type="date" id="admission-date" name="admission_date" placeholder="Choose student's admission date..." label="Date of admission *" value="{{ old('admission_date') }}" autocomplete="off" wire:ignore />
    </div>
</div>
