<form wire:submit="save" class="flex max-w-3xl flex-col gap-8" aria-label="Ask another school for records">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section class="flex flex-col gap-3" aria-labelledby="learner-heading">
        <h2 id="learner-heading" class="text-base font-semibold">Which learner</h2>
        <p class="text-sm text-muted-foreground">Use the admission number the other school gave them. You cannot browse another school's learners.</p>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="holding_school_id" class="text-sm text-muted-foreground">School that holds the records</label>
                <select id="holding_school_id" wire:model="holdingSchoolId" required class="{{ $controlClasses }}" {{ field_error_bindings('holdingSchoolId') }}>
                    <option value="">Choose a school</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="holdingSchoolId" class="mt-1" />
            </div>
            <div>
                <label for="admission_number" class="text-sm text-muted-foreground">Their admission number there</label>
                <input id="admission_number" wire:model="admissionNumber" required maxlength="50" class="{{ $controlClasses }}" {{ field_error_bindings('admissionNumber') }}>
                <x-field-error name="admissionNumber" class="mt-1" />
            </div>
        </div>
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="asking-heading">
        <h2 id="asking-heading" class="text-base font-semibold">What you are asking for</h2>
        <p class="text-sm text-muted-foreground">Ask for the least you need. The other school reads this list and your reason before it decides.</p>
        <fieldset class="grid gap-x-6 sm:grid-cols-2 lg:grid-cols-3" {{ field_error_bindings('categories') }}>
            <legend class="sr-only">Kinds of record</legend>
            @foreach ($dataCategories as $category)
                <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                    <input type="checkbox" wire:model="categories" value="{{ $category->value }}" class="size-4 rounded border-input">
                    {{ $category->label() }}
                </label>
            @endforeach
        </fieldset>
        <x-field-error name="categories" />

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="purpose" class="text-sm text-muted-foreground">Why you need them</label>
                <input id="purpose" wire:model="purpose" required maxlength="500" placeholder="The learner transferred to us in September" class="{{ $controlClasses }}" {{ field_error_bindings('purpose') }}>
                <x-field-error name="purpose" class="mt-1" />
            </div>
            <div>
                <label for="expires_on" class="text-sm text-muted-foreground">Permission ends on (optional)</label>
                <input type="date" id="expires_on" wire:model="expiresOn" min="{{ now()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('expiresOn') }}>
                <x-field-error name="expiresOn" class="mt-1" />
            </div>
        </div>
    </section>

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">
            <x-lucide-send class="mr-2 size-4" />Send the request
        </april:button>
    </div>
</form>
