<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Write an employment record">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <p class="text-sm text-muted-foreground">The person needs an account in this school first. One person holds one employment record per school.</p>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="user_id" class="text-sm text-muted-foreground">Person</label>
            <select id="user_id" wire:model="userId" required class="{{ $controlClasses }}" {{ field_error_bindings('userId') }}>
                <option value="">Choose a person</option>
                @foreach ($people as $person)
                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                @endforeach
            </select>
            <x-field-error name="userId" class="mt-1" />
        </div>
        <div>
            <label for="job_title" class="text-sm text-muted-foreground">Job title (optional)</label>
            <input id="job_title" wire:model="jobTitle" maxlength="100" placeholder="Teacher" class="{{ $controlClasses }}" {{ field_error_bindings('jobTitle') }}>
            <x-field-error name="jobTitle" class="mt-1" />
        </div>
        <div>
            <label for="department" class="text-sm text-muted-foreground">Department (optional)</label>
            <input id="department" wire:model="department" maxlength="100" placeholder="Science" class="{{ $controlClasses }}" {{ field_error_bindings('department') }}>
            <x-field-error name="department" class="mt-1" />
        </div>
        <div>
            <label for="employment_type" class="text-sm text-muted-foreground">Employment</label>
            <select id="employment_type" wire:model="employmentType" required class="{{ $controlClasses }}" {{ field_error_bindings('employmentType') }}>
                @foreach ($employmentTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
            <x-field-error name="employmentType" class="mt-1" />
        </div>
        <div>
            <label for="staff_number" class="text-sm text-muted-foreground">Staff number (optional)</label>
            <input id="staff_number" wire:model="staffNumber" maxlength="30" autocomplete="off" class="{{ $controlClasses }}" {{ field_error_bindings('staffNumber') }}>
            <x-field-error name="staffNumber" class="mt-1" />
        </div>
        <div>
            <label for="joined_on" class="text-sm text-muted-foreground">Joined on (optional)</label>
            <input type="date" id="joined_on" wire:model="joinedOn" class="{{ $controlClasses }}" {{ field_error_bindings('joinedOn') }}>
            <x-field-error name="joinedOn" class="mt-1" />
        </div>
    </div>

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save the record</april:button>
    </div>
</form>
