<div class="row">
    @livewire('display-validation-error')
    <h4 class="text-bold col-12 text-center">Account information</h4> 
    <p class="text-secondary col-12">
        {{__('All fields marked * are required')}}
    </p>
    <div class="col-12">
        <img id="profile-picture" src="{{asset('application-images/user-profile-image.png')}}" alt="Profile Picture" class="rounded-circle profile-image justify-center mx-auto d-block" height="200px" width="200px" >
        <x-adminlte-input-file name="profile_photo" placeholder="Select profile photo" accept="image/*" fgroup-class="col-md-4 mx-auto my-4"  id="profile-image-input">
            
        </x-adminlte-input-file>
    </div>
    <x-adminlte-input name="first_name" label="First name *" placeholder="{{$role}}'s first name" fgroup-class="col-md-3" enable-old-support/>
    <x-adminlte-input name="last_name" label="Last name *" placeholder="{{$role}}'s last name" fgroup-class="col-md-3" enable-old-support/>
    <x-adminlte-input name="other_names" label="Other names *" placeholder="{{$role}}'s other names " fgroup-class="col-md-6" enable-old-support/>
    <x-adminlte-input name="email" type="email" label="Email address *" placeholder="Enter {{$role}}'s email address" fgroup-class="col-md-4" enable-old-support/>
    <x-adminlte-input name="password" label=" Password *" placeholder="input a password" fgroup-class="col-md-4" type="password"/>
    <x-adminlte-input name="password_confirmation" label="Confirm password *" placeholder="input password again" fgroup-class="col-md-4" type="password"/>
    <h4 class="text-bold col-12 text-center">Personal information</h4>
    <x-adminlte-input-date name="birthday" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose {{$role}}'s birthday..." label="Birthday *"  fgroup-class="col-md-3" value="{{old('birthday')}}" autocomplete="off"/>
    <x-adminlte-select name="gender" label="Gender *" fgroup-class="col-md-3" enable-old-support>
        @php ($genders = ['Male', 'Female'])
        @foreach ($genders as $gender)
            <option value="{{$gender}}" >{{$gender}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-select name="blood_group" label="Blood group *" fgroup-class="col-md-3" enable-old-support>
        @php ($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'Ab-', 'O+', 'O-'])
        @foreach ($bloodGroups as $bloodGroup)
            <option value="{{$bloodGroup}}" >{{$bloodGroup}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-input name="phone" label="Phone number" placeholder="{{$role}}'s phone number" fgroup-class="col-md-3" enable-old-support/>
    <x-adminlte-input name="address" placeholder="{{$role}}'s address" fgroup-class="col-md-12 no-resize" label="Address *" enable-old-support/>
    <div class="col-md-8">
        @livewire('nationality-and-state-input-fields', ['nationality' => old('nationality'), 'state' => old('state')])
    </div>
    <x-adminlte-input name="city" label="City *" placeholder="{{$role}}'s city" fgroup-class="col-md-4" enable-old-support/>
    @section('plugins.BsCustomFileInput', true)
    <x-adminlte-select name="religion" label="Religion *" fgroup-class="col-md-6" enable-old-support>
        @php ($religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'])
        @foreach ($religions as $religion)
            <option value="{{$religion}}"  >{{$religion}}</option>
        @endforeach
    </x-adminlte-select>
    @section('plugins.TempusDominusBs4', true)
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("profile-image-input").addEventListener("change", function() {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                var profilePicture = document.getElementById("profile-picture");
                profilePicture.src = e.target.result;
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</div>