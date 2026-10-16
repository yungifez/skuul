<form wire:submit="save" class="flex max-w-3xl flex-col gap-6" aria-label="Edit {{ $school->name }}">
    <x-school-detail-fields
        :countries="$countries"
        :upload="$logo"
        :current-logo-url="$school->logo_path ? $school->logo_url : null"
        :initials-fallback="str($school->name)->substr(0, 2)->upper()->toString()"
    />

    <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-end">
        @can('view', $school)
            <april:button-link href="{{ route('schools.show', $school) }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
        @endcan
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save, logo">{{ $setup ? 'Save and continue' : 'Save changes' }}</april:button>
    </div>
</form>
