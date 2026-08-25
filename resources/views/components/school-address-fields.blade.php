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
        <select
            id="{{ $cityField }}"
            name="{{ $cityField }}"
            x-model="city"
            x-bind:disabled="loading || !country || cities.length === 0"
            required
            autocomplete="address-level2"
            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm disabled:cursor-not-allowed disabled:opacity-60"
        >
            <option value="" x-text="loading ? 'Loading cities…' : (cities.length ? 'Select a city' : 'Select a country first')"></option>
            <template x-for="cityName in cities" :key="cityName">
                <option x-bind:value="cityName" x-text="cityName"></option>
            </template>
        </select>
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
            x-on:change="loadCountry()"
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
