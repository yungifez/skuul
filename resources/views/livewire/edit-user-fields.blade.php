<div class="grid w-full gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-display-validation-errors />
        <p class="text-sm text-muted-foreground">Update the person’s identity and contact details. Account access is managed separately.</p>
    </div>

    <div class="md:col-span-2" x-data="showImage()">
        <div class="flex flex-col items-center gap-3">
            <img id="profile-picture" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="size-24 rounded-full border object-cover shadow-sm">
            <april:input-group type="file" id="profile-image-selector" name="profile_photo" label="Profile picture" class="max-w-sm" @change="showPreview(event)" accept="image/*" />
        </div>
    </div>

    <april:input-group name="name" id="name" label="Full name *" placeholder="{{ $role }}'s full name" value="{{ $user->name }}" />
    <april:input-group name="email" id="email" type="email" label="Email address *" placeholder="Enter {{ $role }}'s email address" value="{{ $user->email }}" />
    <april:input-group type="date" id="birthday" name="birthday" placeholder="Choose {{ $role }}'s date of birth" label="Date of birth" value="{{ $user->birthday }}" />

    <div class="flex flex-col gap-2">
        <april:label for="gender">Gender</april:label>
        <april:select id="gender" name="gender">
            <option value="">Not specified</option>
            @foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $gender)
                <option value="{{ $gender }}" @selected(strtolower((string) $user->gender) === strtolower($gender))>{{ $gender }}</option>
            @endforeach
        </april:select>
    </div>

    <april:input-group id="phone" name="phone" label="Phone number" placeholder="{{ $role }}'s phone number" value="{{ $user->phone }}" />
    <april:input-group id="address" name="address" placeholder="{{ $role }}'s address" label="Address" value="{{ $user->address }}" />

    <div class="md:col-span-2">
        <livewire:nationality-and-state-input-fields :nationality="$user->nationality" :state="$user->state" />
    </div>

    <april:input-group id="city" name="city" label="City" placeholder="{{ $role }}'s city" value="{{ $user->city }}" />
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
