@props(['countries' => [], 'currentLogoUrl' => null, 'initialsFallback' => '', 'upload' => null])

@php
    $controlClasses = 'mt-2 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
@endphp

<section class="space-y-4" aria-labelledby="school-details-heading">
    <h2 id="school-details-heading" class="text-base font-semibold">Details</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name" class="text-sm font-medium">School name *</label>
            <input id="name" wire:model="name" maxlength="255" required autocomplete="organization" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
            <x-field-error name="name" class="mt-1" />
        </div>
        <div>
            <label for="initials" class="text-sm font-medium">Short name</label>
            <input id="initials" wire:model="initials" maxlength="10" placeholder="—" class="{{ $controlClasses }}" {{ field_error_bindings('initials') }}>
            <x-field-error name="initials" class="mt-1" />
        </div>
        <div>
            <label for="phone" class="text-sm font-medium">Phone number</label>
            <input id="phone" type="tel" wire:model="phone" maxlength="255" placeholder="—" autocomplete="tel" class="{{ $controlClasses }}" {{ field_error_bindings('phone') }}>
            <x-field-error name="phone" class="mt-1" />
        </div>
        <div class="sm:col-span-2">
            <label for="email" class="text-sm font-medium">School email</label>
            <input id="email" type="email" wire:model="email" maxlength="255" placeholder="—" autocomplete="email" class="{{ $controlClasses }}" {{ field_error_bindings('email') }}>
            <x-field-error name="email" class="mt-1" />
        </div>
    </div>
</section>

<section class="space-y-4 border-t pt-6" aria-labelledby="school-address-heading">
    <h2 id="school-address-heading" class="text-base font-semibold">Address</h2>
    <x-school-address-fields :countries="$countries" wire />
    @php
        $zones = collect(\DateTimeZone::listIdentifiers())
            ->map(fn (string $zone): array => ['zone' => $zone, 'offset' => (new \DateTime('now', new \DateTimeZone($zone)))->format('P')])
            ->groupBy(fn (array $zone): string => \Illuminate\Support\Str::before($zone['zone'], '/'));
    @endphp
    <div x-data x-init="if (!$wire.timezone) { $wire.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone ?? '' }">
        <label for="timezone" class="text-sm font-medium">Time zone</label>
        <select id="timezone" wire:model="timezone" class="{{ $controlClasses }}" {{ field_error_bindings('timezone') }}>
            <option value="">Server time ({{ config('app.timezone') }})</option>
            @foreach ($zones as $region => $regionZones)
                <optgroup label="{{ $region }}">
                    @foreach ($regionZones as $zone)
                        <option value="{{ $zone['zone'] }}">{{ str_replace(['/', '_'], [' / ', ' '], $zone['zone']) }} (UTC{{ $zone['offset'] }})</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-muted-foreground">Bills, receipts and registers use the date here.</p>
        <x-field-error name="timezone" class="mt-1" />
    </div>
</section>

<section class="space-y-4 border-t pt-6" aria-labelledby="school-logo-heading">
    <h2 id="school-logo-heading" class="text-base font-semibold">Logo</h2>
    <div class="flex items-center gap-4">
        <div class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-background text-xl font-semibold select-none">
            @if ($upload !== null && method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
                <img src="{{ $upload->temporaryUrl() }}" alt="New logo" class="size-full object-cover">
            @elseif ($currentLogoUrl !== null)
                <img src="{{ $currentLogoUrl }}" alt="Current logo" class="size-full object-cover">
            @else
                {{ $initialsFallback === '' ? '—' : $initialsFallback }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <label for="logo" class="text-sm font-medium">{{ $currentLogoUrl === null ? 'Upload a logo' : 'Replace the logo' }}</label>
            <input id="logo" type="file" wire:model="logo" accept="image/*" class="mt-2 block w-full min-w-0 text-sm file:mr-3 file:h-11 file:rounded-md file:border file:border-input file:bg-background file:px-3 file:text-sm" {{ field_error_bindings('logo') }}>
            <p class="mt-1 text-xs text-muted-foreground">A square image up to 5 MB.</p>
            <x-field-error name="logo" class="mt-1" />
        </div>
    </div>
</section>
