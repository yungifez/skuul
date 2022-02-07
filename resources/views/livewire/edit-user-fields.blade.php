<div class="row">
    @livewire('display-validation-error')
    <h4 class="text-bold col-12 text-center">Account information</h4> 
    <x-adminlte-input name="first_name" label="First name" placeholder="{{$role}}'s first name" fgroup-class="col-md-3" enable-old-support value="{{$user->firstName()}}"/>
    <x-adminlte-input name="last_name" label="Last name" placeholder="{{$role}}'s last name" fgroup-class="col-md-3" enable-old-support value="{{$user->lastName()}}"/>
    <x-adminlte-input name="other_names" label="Other names" placeholder="{{$role}}'s other names" fgroup-class="col-md-6" enable-old-support value="{{$user->otherNames()}}"/>
    <x-adminlte-input name="email" label="Email address" placeholder="Enter {{$role}}'s email address" fgroup-class="col-md-4" enable-old-support value="{{$user->email}}"/>
    <h4 class="text-bold col-12 text-center">Personal information</h4>
    <x-adminlte-input-date name="birthday" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose {{$role}}'s birthday..." label="Birthday"  fgroup-class="col-md-3" value="{{old('birthday')}}" value="{{$user->birthday}}"/>
    <x-adminlte-select name="gender" label="Gender" fgroup-class="col-md-3" enable-old-support>
        @php ($genders = ['Male', 'Female'])
        @foreach ($genders as $gender)
            <option value="{{$gender}}" {{Str::lower($gender) == str::lower($user->gender) ? 'selected' : ''}} >{{$gender}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-select name="blood_group" label="Blood group" fgroup-class="col-md-3" enable-old-support>
        @php ($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'Ab-', 'O+', 'O-'])
        @foreach ($bloodGroups as $bloodGroup)
            <option value="{{$bloodGroup}}" {{Str::lower($bloodGroup) == str::lower($user->blood_group) ? 'selected' : ''}} >{{$bloodGroup}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-input name="phone" label="Phone number" placeholder="{{$role}}'s phone number" fgroup-class="col-md-3" enable-old-support value="{{$user->phone}}"/>
    <x-adminlte-input name="address" placeholder="{{$role}}'s address" fgroup-class="col-md-12 no-resize" label="Address" enable-old-support value="{{$user->address}}"/>
    <div class="col-md-8">
        @livewire('nationality-and-state-input-fields', ['nationality' => $user->nationality, 'state' => $user->state])
    </div>
    <x-adminlte-input name="city" label="City" placeholder="{{$role}}'s city" fgroup-class="col-md-4" enable-old-support value="{{$user->city}}"/>
    <x-adminlte-input-file name="photo" placeholder="Choose a profile photo..." accept="image/*" fgroup-class="col-md-6" label="Profile photo (no changes would be made if no file is chosen)">
        <x-slot name="prependSlot">
            <div class="input-group-text bg-lightblue">
                <i class="fas fa-upload"></i>
            </div>
        </x-slot>
    </x-adminlte-input-file>
    <x-adminlte-select name="religion" label="Religion" fgroup-class="col-md-6" enable-old-support>
        @php ($religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'])
        @foreach ($religions as $religion)
            <option value="{{$religion}}" {{Str::lower($religion) == str::lower($user->religion) ? 'selected' : ''}} >{{$religion}}</option>
        @endforeach
    </x-adminlte-select>
</div>