<april:card>
    <slot:title>Edit {{ $academicPeriod->display_name }}</slot:title>
    <slot:description>Update the name, dates, or type before this period is closed.</slot:description>
    <slot:content>
        <form action="{{ route('academic-periods.update', $academicPeriod) }}" method="POST" class="max-w-2xl space-y-4">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Period name" placeholder="Enter a period name" value="{{ old('name', $academicPeriod->name) }}" />
            <div class="grid gap-4 md:grid-cols-2">
                <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" value="{{ $academicPeriod->starts_on?->toDateString() }}" />
                <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" value="{{ $academicPeriod->ends_on?->toDateString() }}" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="type">Period type</april:label>
                <select id="type" name="type" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @foreach (\App\Enums\AcademicPeriodType::cases() as $type)
                        <option value="{{ $type->value }}" {{ old('type', $academicPeriod->type->value) === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            @method('PUT')
            <april:button type="submit" class="w-full md:w-1/2">
                Save changes
            </april:button>
        </form>
    </slot:content>
</april:card>
