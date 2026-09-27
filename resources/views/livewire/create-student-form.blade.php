@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $sectionWord = strtolower(school_term('section', 'section'));
@endphp
<form wire:submit="save" autocomplete="off" class="max-w-3xl space-y-6">
    @include('livewire.partials.person-fields', ['photoUrl' => asset('application-images/user-profile-image.png')])

    <div class="grid gap-4 border-t pt-6 md:grid-cols-2">
        <h2 class="text-base font-semibold md:col-span-2">Admission</h2>
        <div class="md:col-span-2">
            <label for="academic-cycle-section-id" class="text-sm text-muted-foreground">{{ school_term('section', 'Section') }}</label>
            <select id="academic-cycle-section-id" wire:model="academicCycleSectionId" class="{{ $controlClasses }}" {{ field_error_bindings('academicCycleSectionId') }}>
                <option value="">{{ $cycleSections === [] ? "No active {$sectionWord} this year" : "Choose a {$sectionWord}" }}</option>
                @foreach ($cycleSections as $cycleSection)
                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                @endforeach
            </select>
            <x-field-error name="academicCycleSectionId" class="mt-1" />
        </div>
        <div>
            <label for="admission-number" class="text-sm text-muted-foreground">Admission number</label>
            <input id="admission-number" type="text" wire:model="admissionNumber" maxlength="100" placeholder="Made for you when left blank" class="{{ $controlClasses }}" {{ field_error_bindings('admissionNumber') }}>
            <x-field-error name="admissionNumber" class="mt-1" />
        </div>
        <div>
            <label for="admission-date" class="text-sm text-muted-foreground">Date of admission</label>
            <input id="admission-date" type="date" wire:model="admissionDate" max="{{ now()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('admissionDate') }}>
            <x-field-error name="admissionDate" class="mt-1" />
        </div>
    </div>

    <p class="text-sm text-muted-foreground">They get an email with a link to set their own password.</p>
    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save,profilePhoto">Admit learner</april:button>
</form>
