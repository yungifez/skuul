<div class="grid w-full gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-display-validation-errors />
        <p class="text-sm text-muted-foreground">Only information needed to create this person and their school account is requested.</p>
    </div>

    <div class="md:col-span-2" x-data="{ previewUrl: '{{ asset('application-images/user-profile-image.png') }}' }">
        <div class="flex flex-col items-center gap-3">
            <img :src="previewUrl" alt="Profile picture" class="size-24 rounded-full border object-cover shadow-sm">
            <april:input-group type="file" id="profile-image-selector" name="profile_photo" label="Profile picture" class="max-w-sm" @change="if ($event.target.files[0]) previewUrl = URL.createObjectURL($event.target.files[0])" accept="image/*" />
        </div>
    </div>

    <april:input-group name="name" id="name" label="Full name *" placeholder="{{ $role }}'s full name" />
    <april:input-group name="email" id="email" type="email" label="Email address *" placeholder="Enter {{ $role }}'s email address" />

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

    <april:input-group type="date" id="birthday" name="birthday" placeholder="Choose {{ $role }}'s date of birth" label="Date of birth" />

    <div class="flex flex-col gap-2">
        <april:label for="gender">Gender</april:label>
        <april:select id="gender" name="gender">
            <option value="">Not specified</option>
            @foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $gender)
                <option value="{{ $gender }}">{{ $gender }}</option>
            @endforeach
        </april:select>
    </div>

    <april:input-group id="phone" name="phone" label="Phone number" placeholder="{{ $role }}'s phone number" />
    <april:input-group id="address" name="address" placeholder="{{ $role }}'s address" label="Address" />

    <div class="md:col-span-2">
        <livewire:nationality-and-state-input-fields />
    </div>

    <april:input-group id="city" name="city" label="City" placeholder="{{ $role }}'s city" />
</div>
