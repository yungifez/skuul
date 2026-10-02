<div class="max-w-3xl">
    @if ($this->organizations->isEmpty())
        <p class="text-sm text-muted-foreground">You cannot add a campus to any organization. Ask an organization administrator for campus management.</p>
    @else
        <form wire:submit="save" class="flex flex-col gap-6" aria-label="Create a school">
            <section class="space-y-4" aria-labelledby="school-organization-heading">
                <h2 id="school-organization-heading" class="text-base font-semibold">Organization</h2>
                @if ($this->organizations->count() === 1)
                    <p class="text-sm">{{ $this->organizations->first()->name }}</p>
                @else
                    <div>
                        <label for="organization_id" class="text-sm font-medium">Organization *</label>
                        <select id="organization_id" wire:model="organizationId" required class="mt-2 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('organizationId') }}>
                            <option value="">Choose an organization</option>
                            @foreach ($this->organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="organizationId" class="mt-1" />
                    </div>
                @endif
            </section>

            <div class="flex flex-col gap-6 border-t pt-6">
                <x-school-detail-fields :countries="$countries" :suggest-timezone="true" :upload="$logo" :initials-fallback="str($name)->substr(0, 2)->upper()->toString()" />
            </div>

            <div class="flex justify-end border-t pt-6">
                <april:button type="submit" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="save, logo">Create school</april:button>
            </div>
        </form>
    @endif
</div>
