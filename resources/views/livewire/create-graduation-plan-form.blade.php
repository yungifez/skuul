<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Write a graduation plan">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <h2 class="text-base font-semibold">Start with the basics</h2>
    <p class="text-sm text-muted-foreground">Name the plan, then add the classes and subjects after you save. Every item is required by default.</p>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="name" class="text-sm text-muted-foreground">Name</label>
            <input id="name" wire:model="name" required maxlength="100" placeholder="Senior school diploma" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="cohort_id" class="text-sm text-muted-foreground">Who it is for</label>
            <select id="cohort_id" wire:model="cohortId" class="{{ $controlClasses }}" {{ field_error_bindings('cohortId') }}>
                <option value="">Every learner</option>
                @foreach ($cohorts as $cohort)
                    <option value="{{ $cohort->id }}">{{ $cohort->name }}</option>
                @endforeach
            </select>
            <x-field-error name="cohortId" class="mt-1" />
        </div>
        <div class="sm:col-span-2">
            <label for="description" class="text-sm text-muted-foreground">What it is (optional)</label>
            <textarea id="description" wire:model="description" rows="3" maxlength="1000" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
            <x-field-error name="description" class="mt-1" />
        </div>
    </div>

    @include('livewire.partials.graduation-rule-fields', [
        'operatorProperty' => 'completionOperator',
        'countProperty' => 'requiredCount',
        'creditsProperty' => 'requiredCredits',
        'creditsToggleProperty' => 'usesCredits',
        'operator' => $completionOperator,
        'isCountingCredits' => $usesCredits,
    ])

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Write the plan</april:button>
    </div>
</form>
