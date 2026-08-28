<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <h4 class="text-xl font-bold md:text-2xl">Enrollment information</h4>
        <p class="mt-1 text-sm text-muted-foreground">Choose the student’s active {{ strtolower(school_term('section', 'section')) }} for the current academic year.</p>
    </div>
    <div class="flex w-full flex-col gap-2 md:col-span-2">
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
    <div>
        <april:input-group id="admission-number" name="admission_number" label="Admission number" placeholder="Student's admission number" value="{{ old('admission_number') }}" />
    </div>
    <div>
        <april:label for="admission-date">Date of admission *</april:label>
        <input type="date" id="admission-date" name="admission_date" value="{{ old('admission_date') }}" max="{{ now()->toDateString() }}" autocomplete="off" wire:ignore class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
        @error('admission_date')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
</div>
