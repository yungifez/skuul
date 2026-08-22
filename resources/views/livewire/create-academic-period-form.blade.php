<april:card>
    <slot:title>Add a {{ school_term('period', 'period') }}</slot:title>
    <slot:description>Create the next term, semester, break, exam window, or reporting period in {{ current_academic_year()?->name ?? 'the selected academic cycle' }}.</slot:description>
    <slot:content>
        <form action="{{ route('academic-periods.store') }}" method="POST" class="max-w-2xl space-y-4">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Period name" placeholder="For example, Term 1 or Rainy Session" value="{{ old('name') }}" />
            <div class="grid gap-4 md:grid-cols-2">
                <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" />
                <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="type">Period type</april:label>
                <select id="type" name="type" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @foreach (\App\Enums\AcademicPeriodType::cases() as $type)
                        <option value="{{ $type->value }}" {{ old('type', \App\Enums\AcademicPeriodType::Term->value) === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit">Add {{ school_term('period', 'period') }}</april:button>
        </form>
    </slot:content>
</april:card>
