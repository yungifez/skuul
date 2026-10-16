@php
    $controlClasses = 'mt-1 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $section = $timetable->academicCycleSection;
@endphp
<form wire:submit="save" class="max-w-2xl space-y-4">
    <div>
        <label for="timetable-name" class="text-sm text-muted-foreground">Name</label>
        <input id="timetable-name" type="text" wire:model="name" required maxlength="255" placeholder="First term timetable" class="h-11 {{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>
    <div>
        <label for="timetable-description" class="text-sm text-muted-foreground">Description</label>
        <textarea id="timetable-description" wire:model="description" rows="3" maxlength="10000" placeholder="Monday to Friday, with clubs on Thursday" class="py-2 {{ $controlClasses }}" {{ field_error_bindings('description') }}></textarea>
        <x-field-error name="description" class="mt-1" />
    </div>
    <dl class="text-sm">
        <dt class="text-muted-foreground">{{ school_term('section', 'Section') }}</dt>
        <dd>{{ $section ? $section->academicLevel->name.' · '.($section->label ?? $section->name) : '—' }}</dd>
    </dl>
    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save changes</april:button>
</form>
