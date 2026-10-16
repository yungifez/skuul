{{-- The fields an exam is planned with. The caller passes the periods it may sit in. --}}
@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
@endphp
<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="text-sm text-muted-foreground">Name</label>
        <input id="name" wire:model="name" required maxlength="255" placeholder="Mid-term exam" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <label for="academicPeriodId" class="text-sm text-muted-foreground">Reporting period</label>
        <select id="academicPeriodId" wire:model="academicPeriodId" required class="{{ $controlClasses }}" {{ field_error_bindings('academicPeriodId') }}>
            @forelse ($academicPeriods as $period)
                <option value="{{ $period->id }}">{{ $period->displayName }}</option>
            @empty
                <option value="">Create a reporting period first</option>
            @endforelse
        </select>
        <x-field-error name="academicPeriodId" class="mt-1" />
    </div>
    <div>
        <label for="startDate" class="text-sm text-muted-foreground">Starts on</label>
        <input id="startDate" type="date" wire:model="startDate" required class="{{ $controlClasses }}" {{ field_error_bindings('startDate') }}>
        <x-field-error name="startDate" class="mt-1" />
    </div>
    <div>
        <label for="stopDate" class="text-sm text-muted-foreground">Ends on</label>
        <input id="stopDate" type="date" wire:model="stopDate" required class="{{ $controlClasses }}" {{ field_error_bindings('stopDate') }}>
        <x-field-error name="stopDate" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <label for="description" class="text-sm text-muted-foreground">What it covers (optional)</label>
        <textarea id="description" wire:model="description" rows="3" maxlength="10000" class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('description') }}></textarea>
        <x-field-error name="description" class="mt-1" />
    </div>
</div>
