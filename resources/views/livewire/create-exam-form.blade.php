<div class="space-y-6">
    <div>
        <p class="text-sm text-muted-foreground">Create an exam for {{ $academicYear?->name ?? 'the selected school year' }}.</p>
        <p class="mt-1 text-sm text-muted-foreground">The exam will appear on that school year’s calendar and stay attached to its reporting period.</p>
    </div>
    <div class="rounded-xl border bg-card p-6">
        <form action="{{ route('exams.store') }}" method="POST" class="grid gap-5 md:max-w-2xl">
            <x-display-validation-errors/>
            <p class="text-sm text-muted-foreground">
                {{__('All fields marked * are required')}}
            </p>
            <april:input-group id="name" name="name" label="Exam name *" placeholder="Enter exam name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <april:input-group id="start_date" type="date" name="start_date" label="Start date *" required value="{{ old('start_date') }}" />
                <april:input-group type="date" id="stop_date" name="stop_date" label="End date *" required value="{{ old('stop_date') }}" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="select">Reporting period *</april:label>
                <april:select id="select" name="academic_period_id">
                @forelse ($academicPeriods as $item)
                    <option value="{{ $item->id }}" @selected($selectedAcademicPeriodId === $item->id)>{{ $item->displayName }}</option>
                @empty
                    <option value="">Create a reporting period first</option>
                @endforelse
                </april:select>
                @error('academic_period_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-fit">
                <x-lucide-plus class="mr-2 size-4" />
                Create exam
            </april:button>
        </form>
    </div>
</div>
