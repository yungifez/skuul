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
        <select
            id="{{ $countryField }}"
            name="{{ $countryField }}"
            x-model="country"
            x-on:change="loadCountry(false)"
            required
            autocomplete="country-name"
            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
        >
            <option value="">Select a country</option>
            @foreach ($countries as $countryOption)
                @php
                    $countryName = is_array($countryOption) ? $countryOption['name'] : $countryOption->name;
                @endphp
                <option value="{{ $countryName }}" @selected($countryName === $countryValue)>{{ $countryName }}</option>
            @endforeach
        </select>
        @if ($errors->has($countryField))
            <p class="text-sm text-destructive">{{ $errors->first($countryField) }}</p>
        @endif
    </div>

    <div class="flex w-full flex-col gap-2">
        <april:label for="{{ $stateField }}">State / Province *</april:label>
        <select
            id="{{ $stateField }}"
            name="{{ $stateField }}"
            x-model="state"
            x-bind:disabled="loading || !country || states.length === 0"
            required
            autocomplete="address-level1"
            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm disabled:cursor-not-allowed disabled:opacity-60"
        >
            <option value="" x-text="loading ? 'Loading states…' : (states.length ? 'Select a state / province' : 'Select a country first')"></option>
            <template x-for="stateName in states" :key="stateName">
                <option x-bind:value="stateName" x-text="stateName"></option>
            </template>
        </select>
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
