<div class="md:grid grid-cols-12 gap-2 w-full">
    <div class="col-span-12">
        <x-display-validation-errors />
        <p class="text-secondary text-center lg:text-left my-2">
            {{__('All fields marked * are required')}}
        </p>
    </div>
    <div
        x-data="{ previewUrl: '{{ asset('application-images/user-profile-image.png') }}' }"
        class="col-span-12"
    >
        <img :src="previewUrl" alt="Profile Picture" class="w-64 h-64 rounded-full profile-image mx-auto block border border-black dark:border-white shadow">
        <x-input
            type="file"
            id="profile-image-selector"
            name="profile_photo"
            class="hidden"
            label="Select Profile image"
            label-class="border border-input bg-background hover:bg-accent w-72 text-center m-auto rounded"
            @change="if ($event.target.files[0]) previewUrl = URL.createObjectURL($event.target.files[0])"
            accept="image/*"
        />
    </div>
    <x-input name="first_name" id="first-name" label="First name *" placeholder="{{$role}}'s first name" group-class="col-span-3" />
    <x-input name="last_name" id="last-name" label="Last name *" placeholder="{{$role}}'s last name" group-class="col-span-3" />
    <x-input name="other_names" id="other-names" label="Other names *" placeholder="{{$role}}'s other names" group-class="col-span-6" />
    <x-input name="email" id="email" type="email" label="Email address *" placeholder="Enter {{$role}}'s email address" group-class="col-span-6" />
    <div class="col-span-6 flex items-end">
        <april:alert class="w-full">
            <slot:icon>
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </slot:icon>
            <slot:title>{{__('No password needed')}}</slot:title>
            <slot:description>
                {{__('We email this address a one-time link. The :role sets their own password from that link.', ['role' => strtolower($role)])}}
            </slot:description>
        </april:alert>
    </div>
    <h4 class="text-bold text-xl md:text-3xl col-span-12 text-center font-bold">Personal information</h4>
    <x-input type="date" id="birthday" name="birthday" placeholder="Choose {{$role}}'s birthday..." label="Birthday *" group-class="col-span-3  w-full"/>
    <x-select id="gender" name="gender" label="Gender *" group-class="col-span-3" >
        @php ($genders = ['Male', 'Female'])
        @foreach ($genders as $gender)
            <option value="{{$gender}}" >{{$gender}}</option>
        @endforeach
    </x-select>
    <x-select id="blood-group" name="blood_group" label="Blood group *" group-class="col-span-3" >
        @php ($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'Ab-', 'O+', 'O-'])
        @foreach ($bloodGroups as $bloodGroup)
            <option value="{{$bloodGroup}}" >{{$bloodGroup}}</option>
        @endforeach
    </x-select>
    <x-input id="phone" name="phone" label="Phone number" placeholder="{{$role}}'s phone number" group-class="col-span-3" />
    <x-input id="address" name="address" placeholder="{{$role}}'s address" group-class="col-span-9 no-resize" label="Address *" />
    <x-select id="religion" name="religion" label="Religion *" group-class="col-span-3" >
        @php ($religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'])
        @foreach ($religions as $religion)
            <option value="{{$religion}}"  >{{$religion}}</option>
        @endforeach
    </x-select>
    <div class="col-span-8">
        <livewire:nationality-and-state-input-fields />
    </div>
    <x-input id="city" name="city" label="City *" placeholder="{{$role}}'s city" group-class="col-span-4"/>
</div>
