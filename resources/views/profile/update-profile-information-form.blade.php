<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">

        <x-jet-action-message on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="form-group" x-data="{photoName: null, photoPreview: null}">
                <!-- Profile Photo File Input -->
                <input type="file" hidden
                       wire:model="photo"
                       x-ref="photo"
                       x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" class="rounded-circle" height="80px" width="80px">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview">
                    <img x-bind:src="photoPreview" class="rounded-circle" width="80px" height="80px">
                </div>

                <x-jet-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
				</x-jet-secondary-button>
				
				@if ($this->user->profile_photo_path)
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-jet-secondary-button>
                @endif

                <x-jet-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <div class="">
            <!-- Name -->
            <div class="form-group">
                <x-jet-label for="name" value="{{ __('Name') }} ( surname, first name, other names )" />
                <x-jet-input id="name" type="text" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" wire:model.defer="state.name" autocomplete="name" />
                <x-jet-input-error for="name" />
            </div>

            <!-- Email -->
            <div class="form-group">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" type="email" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" wire:model.defer="state.email" />
                <x-jet-input-error for="email" />
            </div>

            <!-- birthday -->
            <x-jet-label for="Birthday" value="{{ __('Birthday') }}" />
            <x-jet-input id="birthday" type="date" class="{{ $errors->has('birthday') ? 'is-invalid' : '' }}"  wire:model="state.birthday"/>
            <x-jet-input-error for="birthday" />
            @section('plugins.TempusDominusBs4', true)

            <div class="row col-12">
                <!--Gender-->
                <x-adminlte-select name="gender" label="Gender" fgroup-class="my-3 col-md-6" enable-old-support wire:model="state.gender">
                    @php ($genders = ['Male', 'Female'])
                    @foreach ($genders as $gender)
                        <option value="{{$gender}}" @selected(Str::lower($gender) == str::lower($this->user->gender)) >{{$gender}}</option>
                    @endforeach
                </x-adminlte-select>

                <!--Blood group-->
                <x-adminlte-select name="blood_group" label="Blood group" fgroup-class="my-3 col-md-6" enable-old-support wire:model="state.blood_group">
                    @php ($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'Ab-', 'O+', 'O-'])
                    @foreach ($bloodGroups as $bloodGroup)
                        <option value="{{$bloodGroup}}" {{Str::lower($bloodGroup) == str::lower($this->user->blood_group) ? 'selected' : ''}} >{{$bloodGroup}}</option>
                    @endforeach
                </x-adminlte-select>
            </div> 

            <!--nationality and state-->
            <div class="mt-3 mx-2">
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

            <div class='row mx-1'>
                <div class=" col-md-6 mb-3">
                    <!--city-->
                    <x-jet-label for="city" value="{{ __('City') }}" />
                    <x-jet-input id="city" type="text" class="{{ $errors->has('city') ? 'is-invalid' : '' }}" wire:model.defer="state.city" />
                    <x-jet-input-error for="city" />
                </div>
                <div class=" col-md-6">
                    <!--phone-->
                    <x-jet-label for="phone" value="{{ __('Phone') }}" />
                    <x-jet-input id="phone" type="text" class="{{ $errors->has('phone') ? 'is-invalid' : '' }}" wire:model.defer="state.phone" />
                    <x-jet-input-error for="phone" />
                </div>
                <x-adminlte-select name="religion" label="Religion" fgroup-class="my-3 col-12" enable-old-support wire:model="state.religion">
                    @php ($religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'])
                    @foreach ($religions as $religion)
                        <option value="{{$religion}}" {{Str::lower($religion) == str::lower($this->user->religion) ? 'selected' : ''}} >{{$religion}}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
		<div class="d-flex align-items-baseline">
			<x-jet-button>
				{{ __('Save') }}
			</x-jet-button>
		</div>
    </x-slot>
</x-jet-form-section>