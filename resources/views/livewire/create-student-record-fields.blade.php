<div class="md:grid grid-cols-12 gap-2">
    <h4 class="text-bold text-xl md:text-3xl font-bold col-span-12 text-center my-2">Enrollment information</h4>
    <div class="flex w-full flex-col gap-2">
        <april:label for="academic-cycle-section-id">Choose a {{ school_term('section', 'home section') }} *</april:label>
        <select id="academic-cycle-section-id" name="academic_cycle_section_id" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">Choose a {{ school_term('section', 'home section') }}</option>
            @foreach ($cycleSections as $cycleSection)
                <option value="{{ $cycleSection['id'] }}" {{ old('academic_cycle_section_id') == $cycleSection['id'] ? 'selected' : '' }}>{{ $cycleSection['level'] }} · {{ $cycleSection['name'] }}</option>
            @endforeach
        </select>
        @error('academic_cycle_section_id')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <april:input-group id="admission-number" name="admission_number" label="Admission number" placeholder="Student's admission number" />
    <april:input-group type="date" id="admission-date" name="admission_date" placeholder="Choose student's admission date..." label="Date of admission  *" value="{{old('admission_date')}}" autocomplete="off" wire:ignore />
</div>
