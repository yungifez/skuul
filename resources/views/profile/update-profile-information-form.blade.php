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
                <input type="file" hidden accept="image/*" wire:model.live="photo" x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                " />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" class="w-40 h-40 rounded-full profile-image mx-auto block border border-black dark:border-white shadow" height="80px" width="80px">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview">
                    <img x-bind:src="photoPreview" class="w-40 h-40 rounded-full profile-image mx-auto block border border-black dark:border-white shadow"  width="80px" height="80px">
                </div>
                <div class="flex items-center justify-center gap-2">
                    <x-button class="mt-2 w-6/12 place-self-center text-sm md:text-base" type="button" x-on:click.prevent="$refs.photo.click()">
                        {{ __('New Photo') }}
                    </x-button>
                    
                    @if ($this->user->profile_photo_path)
                        <x-button type="button" class="mt-2 bg-red-600 w-6/12 place-self-center" wire:click="deleteProfilePhoto">
                            {{ __('Remove Photo') }}
                        </x-button>
                    @endif
                </div>
                @error('photo')
                    <p class="text-red-700 dark:text-red-500 my-2">{{$message}}</p>
                @enderror
            </div>
        @endif
        <div class="md:grid grid-cols-12 gap-4">
            <x-input label="Name" id="name" name="name" placeholder="Your Name In Order First Name, Last Name, Other names " group-class="col-span-12" wire:model.live="state.name"/>
            <x-input label="Email" id="email" name="email" placeholder="Your Email Address" group-class="col-span-12" wire:model.live="state.email"/>
            <x-input type="date" id="birthday" name="birthday" placeholder="Your birthday..." label="Birthday *" group-class="col-span-6" wire:model.live="state.birthday"/>
    
            <x-select id="gender" name="gender" label="Gender *" group-class="col-span-6" wire:model.live="state.gender">
                @php ($genders = ['Male', 'Female'])
                @foreach ($genders as $gender)
                    <option value="{{$gender}}" >{{$gender}}</option>
                @endforeach
            </x-select>
            <!--nationality and state-->
            <div class="col-span-12">
                @livewire('nationality-and-state-input-fields', ['nationality' => ucfirst($this->user->nationality), 'state' => ucfirst($this->user->state)])
            </div>
            
            {{-- listen for change in nationality and state event and set it as the value of their respective state variable. The values of $state is passed on form submit. therefore we set the selected nationality using the browser event fired  --}}
            <script>
                window.addEventListener('nationality-updated',event => {
                    @this.set('state.nationality', event.detail.nationality)
               })
               window.addEventListener('state-updated',event => {
                    @this.set('state.state', event.detail.state)
               })
            </script>
            <x-input id="city" name="city" label="City *" placeholder="Your City" group-class="col-span-6" wire:model.live="state.city"/>

            <x-input id="phone" name="phone" label="Phone number" placeholder="Your phone number" group-class="col-span-6" wire:model.live="state.phone"/>
            <x-textarea id="address" name="address" placeholder="Your Address" group-class="col-span-12" label="Address *" wire:model.live="state.address"/>
            <x-select id="religion" name="religion" label="Religion *" group-class="col-span-6" wire:model.live="state.religion">
                @php ($religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'])
                @foreach ($religions as $religion)
                    <option value="{{$religion}}">{{$religion}}</option>
                @endforeach
            </x-select>
            <x-select id="blood-group" name="blood_group" label="Blood group *" group-class="col-span-6" wire:model.live="state.blood_group">
                @php ($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'Ab-', 'O+', 'O-'])
                @foreach ($bloodGroups as $bloodGroup)
                    <option value="{{$bloodGroup}}">{{$bloodGroup}}</option>
                @endforeach
            </x-select>
        </div>
    </x-slot>
    <x-slot name="actions">
        <x-button class="w-6/12 lg:w-3/12">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-partials.form-section>