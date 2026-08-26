@props([
    'countries' => [],
    'prefix' => '',
    'address' => null,
    'city' => null,
    'country' => null,
    'state' => null,
    'postalCode' => null,
])

@php
    $field = static fn (string $name): string => $prefix === '' ? $name : "{$prefix}_{$name}";
    $addressField = $field('address');
    $cityField = $field('city');
    $countryField = $field('country');
    $stateField = $field('state');
    $postalCodeField = $field('postal_code');
    $countryValue = old($countryField, $country);
@endphp

<div
    class="grid gap-4 sm:grid-cols-2"
    x-data="locationFields({
        country: @js($countryValue),
        state: @js(old($stateField, $state)),
        city: @js(old($cityField, $city)),
        statesUrl: @js(route('locations.states')),
        citiesUrl: @js(route('locations.cities')),
    })"
    x-init="loadCountry()"
>
    <div class="sm:col-span-2">
        <april:input-group
            id="{{ $addressField }}"
            name="{{ $addressField }}"
            type="text"
            placeholder="Enter street address"
            label="Address *"
            value="{{ old($addressField, $address) }}"
            autocomplete="address-line1"
            required
        />
        @if ($errors->has($addressField))
            <p class="mt-1 text-sm text-destructive">{{ $errors->first($addressField) }}</p>
        @endif
    </div>

    <div class="flex w-full flex-col gap-2">
        <april:label for="{{ $cityField }}">City *</april:label>
        <april:combobox
            name="{{ $cityField }}"
            value="{{ old($cityField, $city) }}"
            placeholder="Start typing or choose a city"
            x-model="city"
            x-on:input="selectedValue = $event.target.value; city = $event.target.value"
            x-on:change="selectedValue = $event.detail.value; city = $event.detail.value"
            required
            autocomplete="address-level2"
            class="w-full"
        >
            <slot:trigger>
                <button
                    id="{{ $cityField }}"
                    type="button"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-left text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="truncate" x-text="city || 'Start typing or choose a city'"></span>
                    <span aria-hidden="true" class="ml-2 text-muted-foreground">⌄</span>
                </button>
            </slot:trigger>
            <slot:empty>No matching city. You can keep the city you typed.</slot:empty>
            <template x-if="city && !cities.includes(city)">
                <div
                    data-slot="combobox-option"
                    role="option"
                    x-bind="option"
                    x-bind:data-value="city"
                    tabindex="-1"
                    class="flex cursor-default items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[active=true]:bg-accent data-[active=true]:text-accent-foreground"
                >
                    <span class="mr-2 flex size-4 items-center justify-center" aria-hidden="true">
                        <span x-show="isSelectedValue(city)">✓</span>
                    </span>
                    <span x-text="city"></span>
                </div>
            </template>
            <template x-for="cityName in cities" :key="cityName">
                <div
                    data-slot="combobox-option"
                    role="option"
                    x-bind="option"
                    x-bind:data-value="cityName"
                    tabindex="-1"
                    class="flex cursor-default items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[active=true]:bg-accent data-[active=true]:text-accent-foreground"
                >
                    <span class="mr-2 flex size-4 items-center justify-center" aria-hidden="true">
                        <span x-show="isSelectedValue(cityName)">✓</span>
                    </span>
                    <span x-text="cityName"></span>
                </div>
            </template>
        </april:combobox>
        @if ($errors->has($cityField))
            <p class="text-sm text-destructive">{{ $errors->first($cityField) }}</p>
        @endif
    </div>

    <div class="flex w-full flex-col gap-2">
        <april:label for="{{ $countryField }}">Country *</april:label>
        <april:combobox
            name="{{ $countryField }}"
            value="{{ $countryValue }}"
            placeholder="Search countries"
            x-model="country"
            x-on:change="selectedValue = $event.detail.value; country = $event.detail.value; loadCountry(false)"
            required
            autocomplete="country-name"
        >
            <slot:trigger>
                <button
                    id="{{ $countryField }}"
                    type="button"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-left text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="truncate" x-text="country || 'Select a country'"></span>
                    <span aria-hidden="true" class="ml-2 text-muted-foreground">⌄</span>
                </button>
            </slot:trigger>
            <slot:empty>No matching country.</slot:empty>
            <april:combobox-option value="">Select a country</april:combobox-option>
            @foreach ($countries as $countryOption)
                @php
                    $countryName = is_array($countryOption) ? $countryOption['name'] : $countryOption->name;
                @endphp
                <april:combobox-option value="{{ $countryName }}">{{ $countryName }}</april:combobox-option>
            @endforeach
        </april:combobox>
        @if ($errors->has($countryField))
            <p class="text-sm text-destructive">{{ $errors->first($countryField) }}</p>
        @endif
    </div>

    <div class="flex w-full flex-col gap-2">
        <april:label for="{{ $stateField }}">State / Province *</april:label>
        <april:combobox
            name="{{ $stateField }}"
            value="{{ old($stateField, $state) }}"
            placeholder="Search states or provinces"
            x-model="state"
            x-on:change="selectedValue = $event.detail.value; state = $event.detail.value"
            required
            autocomplete="address-level1"
        >
            <slot:trigger>
                <button
                    id="{{ $stateField }}"
                    type="button"
                    x-bind:disabled="loading || !country || states.length === 0"
                    x-on:click="if (loading || !country || states.length === 0) $event.stopPropagation()"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-left text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span class="truncate" x-text="state || (loading ? 'Loading states…' : (country ? 'Select a state / province' : 'Select a country first'))"></span>
                    <span aria-hidden="true" class="ml-2 text-muted-foreground">⌄</span>
                </button>
            </slot:trigger>
            <slot:empty>No matching state or province.</slot:empty>
            <template x-for="stateName in states" :key="stateName">
                <div
                    data-slot="combobox-option"
                    role="option"
                    x-bind="option"
                    x-bind:data-value="stateName"
                    tabindex="-1"
                    class="flex cursor-default items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[active=true]:bg-accent data-[active=true]:text-accent-foreground"
                >
                    <span class="mr-2 flex size-4 items-center justify-center" aria-hidden="true">
                        <span x-show="isSelectedValue(stateName)">✓</span>
                    </span>
                    <span x-text="stateName"></span>
                </div>
            </template>
        </april:combobox>
        @if ($errors->has($stateField))
            <p class="text-sm text-destructive">{{ $errors->first($stateField) }}</p>
        @endif
    </div>

    <april:input-group
        id="{{ $postalCodeField }}"
        name="{{ $postalCodeField }}"
        type="text"
        placeholder="Enter postal or ZIP code"
        label="Postal / ZIP code *"
        value="{{ old($postalCodeField, $postalCode) }}"
        autocomplete="postal-code"
        required
    />
    @if ($errors->has($postalCodeField))
        <p class="text-sm text-destructive">{{ $errors->first($postalCodeField) }}</p>
    @endif
</div>
