{{-- The fields a paper of an exam is written with. --}}
@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
@endphp
<div class="grid gap-4 sm:grid-cols-3">
    <div class="sm:col-span-2">
        <label for="name" class="text-sm text-muted-foreground">Paper</label>
        <input id="name" wire:model="name" required maxlength="255" placeholder="Mathematics paper 1" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>
    <div>
        <label for="totalMarks" class="text-sm text-muted-foreground">Highest mark</label>
        <input id="totalMarks" type="number" inputmode="numeric" min="1" max="1000" wire:model="totalMarks" required class="{{ $controlClasses }}" {{ field_error_bindings('totalMarks') }}>
        <x-field-error name="totalMarks" class="mt-1" />
    </div>
    <div class="sm:col-span-3">
        <label for="description" class="text-sm text-muted-foreground">What it covers (optional)</label>
        <textarea id="description" wire:model="description" rows="3" maxlength="10000" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
        <x-field-error name="description" class="mt-1" />
    </div>
</div>
