<div class="grid w-full gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-display-validation-errors />
        <p class="text-sm text-muted-foreground">Update the person’s identity and contact details. Account access is managed separately.</p>
    </div>

    <div class="md:col-span-2" x-data="showImage()">
        <div class="flex flex-col items-center gap-3">
            <img id="profile-picture" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="size-24 rounded-full border object-cover shadow-sm">
            <april:input-group type="file" id="profile-image-selector" name="profile_photo" label="Profile picture" class="max-w-sm" @change="showPreview(event)" accept="image/jpeg,image/png" />
        </div>
    </div>

    <april:input-group name="name" id="name" label="Full name *" placeholder="{{ $role }}'s full name" value="{{ old('name', $user->name) }}" />
    <april:input-group name="email" id="email" type="email" label="Email address *" placeholder="Enter {{ $role }}'s email address" value="{{ old('email', $user->email) }}" />
    <april:input-group type="date" id="birthday" name="birthday" placeholder="Choose {{ $role }}'s date of birth" label="Date of birth" value="{{ old('birthday', $user->birthday?->toDateString()) }}" />

    <div class="flex flex-col gap-2">
        <april:label for="gender">Gender</april:label>
        <april:select id="gender" name="gender">
            <option value="">Not specified</option>
            @foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $user->gender) === $gender)>{{ $gender }}</option>
            @endforeach
        </april:select>
    </div>

    <april:input-group id="phone" name="phone" label="Phone number" placeholder="{{ $role }}'s phone number" value="{{ old('phone', $user->phone) }}" />
    <april:input-group id="address" name="address" placeholder="{{ $role }}'s address line 1" label="Address line 1" value="{{ old('address', $user->address) }}" />
    <april:input-group id="address-line-2" name="address_line_2" placeholder="Apartment, suite, or unit" label="Address line 2" value="{{ old('address_line_2', $user->address_line_2) }}" />

    <div class="md:col-span-2">
        <livewire:nationality-and-state-input-fields :country="old('country', $user->country)" :state="old('state', $user->state)" :nationality="old('nationality', $user->nationality)" />
    </div>

    <april:input-group id="city" name="city" label="City" placeholder="{{ $role }}'s city" value="{{ old('city', $user->city) }}" />
    <april:input-group id="postal-code" name="postal_code" label="Postal / ZIP code" placeholder="Postal or ZIP code" value="{{ old('postal_code', $user->postal_code) }}" />
</div>

@pushOnce('scripts')
    <script>
        function showImage() {
            return {
                showPreview(event) {
                    if (event.target.files.length > 0) {
                        document.getElementById('profile-picture').src = URL.createObjectURL(event.target.files[0]);
                    }
                }
            };
        }
    </script>
@endPushOnce
