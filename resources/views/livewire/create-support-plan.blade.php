<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <form wire:submit="save" class="flex flex-col gap-6" aria-label="Open a support plan">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex flex-col gap-2">
                <label for="student-record" class="text-sm font-medium">Learner</label>
                <select id="student-record" wire:model="studentRecordId" class="{{ $controlClasses }}" {{ field_error_bindings('studentRecordId') }}>
                    <option value="">Choose</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}</option>
                    @endforeach
                </select>
                <x-field-error name="studentRecordId" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="category" class="text-sm font-medium">Kind of help</label>
                <select id="category" wire:model.live="category" class="{{ $controlClasses }}" {{ field_error_bindings('category') }}>
                    @foreach ($categories as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>
                @if ($isConfidential)
                    <p class="inline-flex items-center gap-1 text-sm text-muted-foreground" id="confidential-mark"><x-lucide-lock class="size-3.5" aria-hidden="true" /> Confidential</p>
                @endif
                <x-field-error name="category" />
            </div>

            <div class="flex flex-col gap-2 sm:col-span-2">
                <label for="title" class="text-sm font-medium">Title</label>
                <input id="title" wire:model="title" maxlength="255" placeholder="Extra reading, four mornings a week" class="{{ $controlClasses }}" {{ field_error_bindings('title') }}>
                <x-field-error name="title" />
            </div>

            <div class="flex flex-col gap-2 sm:col-span-2">
                <label for="summary" class="text-sm font-medium">Summary</label>
                <textarea id="summary" wire:model="summary" rows="4" maxlength="5000" placeholder="What the child needs and what the school agreed"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('summary') }}></textarea>
                <x-field-error name="summary" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="starts-on" class="text-sm font-medium">Starts</label>
                <input type="date" id="starts-on" wire:model="startsOn" class="{{ $controlClasses }}" {{ field_error_bindings('startsOn') }}>
                <x-field-error name="startsOn" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="review-on" class="text-sm font-medium">Review</label>
                <input type="date" id="review-on" wire:model="reviewOn" class="{{ $controlClasses }}" {{ field_error_bindings('reviewOn') }}>
                <x-field-error name="reviewOn" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="assigned-to" class="text-sm font-medium">Run by</label>
                <select id="assigned-to" wire:model="assignedTo" class="{{ $controlClasses }}" {{ field_error_bindings('assignedTo') }}>
                    <option value="">Nobody yet</option>
                    @foreach ($staff as $person)
                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="assignedTo" />
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ route('support-plans.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Open the plan</april:button>
        </div>
    </form>
</div>
