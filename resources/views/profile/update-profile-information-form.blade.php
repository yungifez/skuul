<x-partials.form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <x-action-message on="saved">
            {{ __('Saved.') }}
        </x-action-message>
        <p class="text-sm text-muted-foreground">
            {{ __('All fields marked * are required.') }}
        </p>

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="flex flex-col gap-4 rounded-lg border bg-muted/20 p-4 sm:flex-row sm:items-center sm:justify-between"
                x-data="{ photoName: null, photoPreview: null }">
                <input type="file" hidden accept="image/*" wire:model.live="photo" x-ref="photo" x-on:change="
                    photoName = $refs.photo.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (event) => { photoPreview = event.target.result; };
                    reader.readAsDataURL($refs.photo.files[0]);
                " />

                <div class="flex items-center gap-4">
                    <div x-show="! photoPreview">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ __('Current profile photo') }}"
                            class="size-20 rounded-full border object-cover shadow-sm" width="80" height="80">
                    </div>
                    <div x-show="photoPreview">
                        <img x-bind:src="photoPreview" alt="{{ __('New profile photo preview') }}"
                            class="size-20 rounded-full border object-cover shadow-sm" width="80" height="80">
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="font-medium">{{ __('Profile photo') }}</p>
                        <p class="text-sm text-muted-foreground">{{ __('Use a clear image that helps people recognize you.') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <april:button type="button" size="sm" variant="outline" x-on:click.prevent="$refs.photo.click()">
                        {{ __('Choose photo') }}
                    </april:button>

                    @if ($this->user->profile_photo_path)
                        <april:button type="button" size="sm" variant="destructive" wire:click="deleteProfilePhoto">
                            {{ __('Remove') }}
                        </april:button>
                    @endif
                </div>
            </div>
            @error('photo')
                <p class="text-sm text-destructive">{{ $message }}</p>
            @enderror
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <april:input-group label="Full name *" id="name" name="name" placeholder="Your full name" wire:model.live="state.name" />
            <april:input-group label="Email *" id="email" name="email" placeholder="Your email address" wire:model.live="state.email" />
            <april:input-group type="date" id="birthday" name="birthday" label="Date of birth" wire:model.live="state.birthday" />

            <div class="flex w-full flex-col gap-2">
                <april:label for="gender">Gender</april:label>
                <april:select id="gender" name="gender" wire:model.live="state.gender">
                @php ($genders = ['', 'Male', 'Female', 'Non-binary', 'Prefer not to say'])
                @foreach ($genders as $gender)
                <option value="{{$gender}}">{{$gender ?: 'Not specified'}}</option>
                @endforeach

                </april:select>
                @error('gender')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <april:input-group id="nationality" name="nationality" label="Nationality" placeholder="Your nationality" wire:model.live="state.nationality" />
            <april:input-group id="phone" name="phone" label="Phone number" placeholder="Your phone number" wire:model.live="state.phone" />

            <div class="flex flex-col gap-1 border-t pt-6 sm:col-span-2">
                <h3 class="font-semibold">{{ __('Address') }}</h3>
                <p class="text-sm text-muted-foreground">
                    {{ __('Use the same format you would use for a billing address.') }}
                </p>
            </div>

            <div class="sm:col-span-2">
                @livewire('nationality-and-state-input-fields', ['country' => ucfirst($this->user->country ?? $this->user->nationality),
                'state' => ucfirst($this->user->state)])
            </div>

            <script>
                window.addEventListener('country-updated', event => {
                    @this.set('state.country', event.detail.country)
                })
                window.addEventListener('state-updated', event => {
                    @this.set('state.state', event.detail.state)
                })
            </script>

            <april:input-group id="address" name="address" label="Address line 1" placeholder="Street address" autocomplete="street-address" wire:model.live="state.address" />
            <april:input-group id="address_line_2" name="address_line_2" label="Address line 2" placeholder="Apartment, suite, unit (optional)" autocomplete="address-line2" wire:model.live="state.address_line_2" />
            <april:input-group id="city" name="city" label="City" placeholder="Your city" autocomplete="address-level2" wire:model.live="state.city" />
            <april:input-group id="postal_code" name="postal_code" label="Postal / ZIP code" placeholder="Postal or ZIP code" autocomplete="postal-code" wire:model.live="state.postal_code" />

        </div>
    </x-slot>
    <x-slot name="actions">
        <april:button>
            {{ __('Save') }}
        </april:button>
    </x-slot>
</x-partials.form-section>
