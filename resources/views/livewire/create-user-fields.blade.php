<div class="grid w-full gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-display-validation-errors />
        <p class="text-sm text-muted-foreground">Only information needed to create this person and their school account is requested.</p>
    </div>

    <div class="md:col-span-2" x-data="{ previewUrl: '{{ asset('application-images/user-profile-image.png') }}' }">
        <div class="flex flex-col items-center gap-3">
            <img :src="previewUrl" alt="Profile picture" class="size-24 rounded-full border object-cover shadow-sm">
            <april:input-group type="file" id="profile-image-selector" name="profile_photo" label="Profile picture" class="max-w-sm" @change="if ($event.target.files[0]) previewUrl = URL.createObjectURL($event.target.files[0])" accept="image/jpeg,image/png" />
        </div>
    </div>

    <april:input-group name="name" id="name" label="Full name *" placeholder="{{ $role }}'s full name" value="{{ old('name') }}" />
    <april:input-group name="email" id="email" type="email" label="Email address *" placeholder="Enter {{ $role }}'s email address" value="{{ old('email') }}" />

    <div class="md:col-span-2">
        <april:alert>
            <slot:icon><x-lucide-mail class="size-4" /></slot:icon>
            <slot:title>No password needed</slot:title>
            <slot:description>We email this person a one-time link to set their own password.</slot:description>
        </april:alert>
    </div>

    <div class="border-t pt-4 md:col-span-2">
        <h3 class="font-semibold">Optional profile details</h3>
        <p class="text-sm text-muted-foreground">These details support school operations and can be completed later.</p>
    </div>

    <april:input-group type="date" id="birthday" name="birthday" placeholder="Choose {{ $role }}'s date of birth" label="Date of birth" value="{{ old('birthday') }}" />

    <div class="flex flex-col gap-2">
        <april:label for="gender">Gender</april:label>
        <april:select id="gender" name="gender">
            <option value="">Not specified</option>
            @foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
            @endforeach
        </april:select>
    </div>

    <april:input-group id="phone" name="phone" label="Phone number" placeholder="{{ $role }}'s phone number" value="{{ old('phone') }}" />
    <april:input-group id="address" name="address" placeholder="{{ $role }}'s address line 1" label="Address line 1" value="{{ old('address') }}" />
    <april:input-group id="address-line-2" name="address_line_2" placeholder="Apartment, suite, or unit" label="Address line 2" value="{{ old('address_line_2') }}" />

    <div class="md:col-span-2">
        <livewire:nationality-and-state-input-fields :country="old('country')" :state="old('state')" :nationality="old('nationality')" />
    </div>

    <april:input-group id="city" name="city" label="City" placeholder="{{ $role }}'s city" value="{{ old('city') }}" />
    <april:input-group id="postal-code" name="postal_code" label="Postal / ZIP code" placeholder="Postal or ZIP code" value="{{ old('postal_code') }}" />
</div>
