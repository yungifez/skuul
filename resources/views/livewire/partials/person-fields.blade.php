{{-- The identity and contact details of a person. The caller passes $controlClasses and $photoUrl. --}}
@php
    $previewUrl = $photoUrl;

    try {
        if ($profilePhoto && method_exists($profilePhoto, 'temporaryUrl')) {
            $previewUrl = $profilePhoto->temporaryUrl();
        }
    } catch (\Throwable) {
        $previewUrl = $photoUrl;
    }
@endphp
<div class="grid gap-4 md:grid-cols-2">
    <div class="flex items-center gap-4 md:col-span-2">
        <img src="{{ $previewUrl }}" alt="" class="size-16 shrink-0 rounded-full border object-cover">
        <div class="min-w-0 flex-1">
            <label for="profile-photo" class="text-sm text-muted-foreground">Profile picture</label>
            <input id="profile-photo" type="file" accept="image/jpeg,image/png" wire:model="profilePhoto" class="mt-1 block w-full text-sm file:mr-3 file:h-11 file:rounded-md file:border file:border-input file:bg-background file:px-3 file:text-sm" {{ field_error_bindings('profilePhoto') }}>
            <p wire:loading wire:target="profilePhoto" class="mt-1 text-sm text-muted-foreground">Uploading…</p>
            <x-field-error name="profilePhoto" class="mt-1" />
        </div>
    </div>

    <div>
        <label for="person-name" class="text-sm text-muted-foreground">Full name</label>
        <input id="person-name" type="text" wire:model="name" required maxlength="100" autocomplete="off" placeholder="Ada Bell" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
        <x-field-error name="name" class="mt-1" />
    </div>
    <div>
        <label for="person-email" class="text-sm text-muted-foreground">Email address</label>
        <input id="person-email" type="email" wire:model="email" required maxlength="100" autocomplete="off" placeholder="ada.bell@example.com" class="{{ $controlClasses }}" {{ field_error_bindings('email') }}>
        <x-field-error name="email" class="mt-1" />
    </div>
    <div>
        <label for="person-birthday" class="text-sm text-muted-foreground">Date of birth</label>
        <input id="person-birthday" type="date" wire:model="birthday" max="{{ now()->subDay()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('birthday') }}>
        <x-field-error name="birthday" class="mt-1" />
    </div>
    <div>
        <label for="person-gender" class="text-sm text-muted-foreground">Gender</label>
        <select id="person-gender" wire:model="gender" class="{{ $controlClasses }}" {{ field_error_bindings('gender') }}>
            <option value="">Not specified</option>
            @foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $genderOption)
                <option value="{{ $genderOption }}">{{ $genderOption }}</option>
            @endforeach
        </select>
        <x-field-error name="gender" class="mt-1" />
    </div>
    <div>
        <label for="person-phone" class="text-sm text-muted-foreground">Phone number</label>
        <input id="person-phone" type="tel" wire:model="phone" maxlength="100" placeholder="+234 801 234 5678" class="{{ $controlClasses }}" {{ field_error_bindings('phone') }}>
        <x-field-error name="phone" class="mt-1" />
    </div>
    <div>
        <label for="person-nationality" class="text-sm text-muted-foreground">Nationality</label>
        <input id="person-nationality" type="text" wire:model="nationality" maxlength="100" placeholder="Nigerian" class="{{ $controlClasses }}" {{ field_error_bindings('nationality') }}>
        <x-field-error name="nationality" class="mt-1" />
    </div>
    <div>
        <label for="person-address" class="text-sm text-muted-foreground">Address line 1</label>
        <input id="person-address" type="text" wire:model="address" maxlength="255" placeholder="12 Palm Avenue" class="{{ $controlClasses }}" {{ field_error_bindings('address') }}>
        <x-field-error name="address" class="mt-1" />
    </div>
    <div>
        <label for="person-address-line-2" class="text-sm text-muted-foreground">Address line 2</label>
        <input id="person-address-line-2" type="text" wire:model="addressLine2" maxlength="255" placeholder="Flat 3" class="{{ $controlClasses }}" {{ field_error_bindings('addressLine2') }}>
        <x-field-error name="addressLine2" class="mt-1" />
    </div>
    <div class="md:col-span-2">
        <livewire:nationality-and-state-input-fields :country="$country === '' ? null : $country" :state="$state === '' ? null : $state" :show-nationality="false" wire:key="person-country-and-state" />
    </div>
    <div>
        <label for="person-city" class="text-sm text-muted-foreground">City</label>
        <input id="person-city" type="text" wire:model="city" maxlength="100" placeholder="Lagos" class="{{ $controlClasses }}" {{ field_error_bindings('city') }}>
        <x-field-error name="city" class="mt-1" />
    </div>
    <div>
        <label for="person-postal-code" class="text-sm text-muted-foreground">Postal / ZIP code</label>
        <input id="person-postal-code" type="text" wire:model="postalCode" maxlength="30" placeholder="100001" class="{{ $controlClasses }}" {{ field_error_bindings('postalCode') }}>
        <x-field-error name="postalCode" class="mt-1" />
    </div>
</div>
