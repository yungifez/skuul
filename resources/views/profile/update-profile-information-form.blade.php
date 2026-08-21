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
        <p class="text-secondary text-center lg:text-left my-2">
            {{__('All fields marked * are required')}}
        </p>
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div class="flex flex-col justify-center" x-data="{photoName: null, photoPreview: null}">
            <!-- Profile Photo File Input -->
            <input type="file" hidden accept="image/*" wire:model.live="photo" x-ref="photo" x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                " />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $this->user->profile_photo_url }}"
                    class="w-40 h-40 rounded-full profile-image mx-auto block border border-black dark:border-white shadow"
                    height="80px" width="80px">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview">
                <img x-bind:src="photoPreview"
                    class="w-40 h-40 rounded-full profile-image mx-auto block border border-black dark:border-white shadow"
                    width="80px" height="80px">
            </div>
            <div class="flex items-center justify-center gap-2">
                <april:button class="mt-2 w-6/12 place-self-center text-sm md:text-base" type="button"
                    x-on:click.prevent="$refs.photo.click()">
                    {{ __('New Photo') }}
                </april:button>

                @if ($this->user->profile_photo_path)
                <april:button type="button" variant="destructive" class="mt-2 w-6/12 place-self-center"
                    wire:click="deleteProfilePhoto">
                    {{ __('Remove Photo') }}
                </april:button>
                @endif
            </div>
            @error('photo')
            <p class="text-red-700 dark:text-red-500 my-2">{{$message}}</p>
            @enderror
        </div>
        @endif
        <div class="md:grid grid-cols-12 gap-4">
            <april:input-group label="Full name *" id="name" name="name" placeholder="Your full name" wire:model.live="state.name" />
            <april:input-group label="Email *" id="email" name="email" placeholder="Your email address" wire:model.live="state.email" />
            <april:input-group type="date" id="birthday" name="birthday" placeholder="Your date of birth..." label="Date of birth" wire:model.live="state.birthday" />

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
            <!--nationality and state-->
            <div class="col-span-12">
                @livewire('nationality-and-state-input-fields', ['nationality' => ucfirst($this->user->nationality),
                'state' => ucfirst($this->user->state)])
            </div>

            {{-- listen for change in nationality and state event and set it as the value of their respective state
            variable. The values of $state is passed on form submit. therefore we set the selected nationality using the
            browser event fired --}}
            <script>
                window.addEventListener('nationality-updated', event => {
                    @this.set('state.nationality', event.detail.nationality)
                })
                window.addEventListener('state-updated', event => {
                    @this.set('state.state', event.detail.state)
                })
            </script>
            <april:input-group id="city" name="city" label="City" placeholder="Your city" wire:model.live="state.city" />

            <april:input-group id="phone" name="phone" label="Phone number" placeholder="Your phone number" wire:model.live="state.phone" />
            <div class="col-span-12 flex w-full flex-col gap-2">
                <april:label for="address">Address</april:label>
                <april:textarea id="address" name="address" placeholder="Your Address" wire:model.live="state.address" />
            </div>
        </div>
    </x-slot>
    <x-slot name="actions">
        <april:button class="w-6/12 lg:w-3/12">
            {{ __('Save') }}
        </april:button>
    </x-slot>
</x-partials.form-section>
