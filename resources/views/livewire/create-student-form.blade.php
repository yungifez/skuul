<form action="{{route('students.store')}}" method="POST" enctype="multipart/form-data">
    <div class="row">
       
        
        <h4 class="text-bold col-12 text-center">Account information</h4> 
        @if ($errors->any())
            <div class="alert alert-danger col-12">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <x-adminlte-input name="first_name" label="First name" placeholder="Student's first name" fgroup-class="col-md-3" enable-old-support/>
        <x-adminlte-input name="last_name" label="Last name" placeholder="Student's last name" fgroup-class="col-md-3" enable-old-support/>
        <x-adminlte-input name="other_names" label="Other names" placeholder="Student's other names" fgroup-class="col-md-6" enable-old-support/>
        <x-adminlte-input name="email" label="Email address" placeholder="Enter student's email address" fgroup-class="col-md-4" enable-old-support/>
        <x-adminlte-input name="password" label=" Password" placeholder="input a password" fgroup-class="col-md-4" type="password"/>
        <x-adminlte-input name="password_confirmation" label="Confirm password" placeholder="input password again" fgroup-class="col-md-4" type="password"/>
        <h4 class="text-bold col-12 text-center">Personal information</h4>
        <x-adminlte-input-date name="birthday" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose student's birthday..." label="Birthday"  fgroup-class="col-md-3" value="{{old('birthday')}}"/>
        <x-adminlte-select name="gender" label="Gender" fgroup-class="col-md-3" enable-old-support>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </x-adminlte-select>
        <x-adminlte-select name="blood_group" label="Blood group" fgroup-class="col-md-3" enable-old-support>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">Ab-</option>
            <option value="0+">O+</option>
            <option value="O-">O-</option>
        </x-adminlte-select>
        <x-adminlte-input name="phone" label="Phone number" placeholder="Student's phone number" fgroup-class="col-md-3" enable-old-support/>
        <x-adminlte-input name="address" placeholder="Student's address" fgroup-class="col-md-12 no-resize" label="Address" enable-old-support/>
        <x-adminlte-select name="nationality" label="Nationality" fgroup-class="col-md-3"  wire:model="country" enable-old-support>
            @foreach ($countries as $item)
                <option value="{{$item}}">{{$item}}</option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-select2 name="state" label="State" fgroup-class="col-md-4" wire:model="state" enable-old-support>
            @if (isset($states))
                @foreach ($states as $item)
                    <option value="{{$item}}">{{$item}}</option>
                @endforeach
            @else 
                <option value="" disabled>Select a country first</option>
            @endif
        </x-adminlte-select2>
        <x-adminlte-input name="city" label="City" placeholder="Student's city" fgroup-class="col-md-4" enable-old-support/>
        <x-adminlte-input-file name="profile_photo" placeholder="Choose a profile photo..." accept="image/*" fgroup-class="col-md-6" label="Profile photo">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-lightblue">
                    <i class="fas fa-upload"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>
        <x-adminlte-select name="religion" label="Religion" fgroup-class="col-md-6" enable-old-support>
            <option value="christianity">Christianity</option>
            <option value="islam">Islam</option>
            <option value="others">Others</option>
        </x-adminlte-select>
        <h4 class="text-bold col-12 text-center">Class information</h4>
        <x-adminlte-select name="my_class_id" label="Choose a class" fgroup-class="col-md-6" wire:model="myClass">
            @foreach ($myClasses as $item)
                <option value="{{$item['id']}}">{{$item['name']}}</option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-select name="section_id" label="Choose a section" fgroup-class="col-md-6" wire:model="section">
            @if (isset($sections))
                @foreach ($sections as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            @else
                <option value="" disabled>Select a class first</option>
            @endif
        </x-adminlte-select>
        <x-adminlte-input name="admission_number" label="Admission number ( would be automatically created if left blank )" placeholder="Student's admission numbaer" fgroup-class="col-md-6" enable-old-support/>
        <x-adminlte-input-date name="admission_date" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose student's admission date..." label="Date of admission"  fgroup-class="col-md-6" value="{{old('admission_date')}}"/>
        @csrf
        <div class='col-12 my-2'>
            <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
        </div>
    </div>
</form>