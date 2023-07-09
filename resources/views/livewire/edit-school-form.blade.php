<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit {{$school->name}}</h4>
    </div>
    <div class="card-body">
        <form action="{{route('schools.update', $school->id )}}" method="POST" class="md:w-6/12" enctype="multipart/form-data">
            <x-display-validation-errors />
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <x-input id="name" name="name" placeholder="Enter name of school" label="School Name *" value="{{$school->name}}" />
            <x-textarea id="address" name="address" placeholder="Enter school branch address" label="School Address *" >
                {{$school->address}}
            </x-textarea>
            <x-input id="initials" name="initials" placeholder="Enter school initials" label="School Initials" value="{{$school->initials}}" />   
            <x-input id="phone" name="phone" type="tel" placeholder="Enter school phone number" label="School Phone Number" value="{{ $school->phone}}"  />
            <x-input id="email" name="email" type="email" placeholder="Enter school email" label="School Email" value="{{ $school->email}}"  />
            <x-input name="logo" id="logo" type="file"  label="Logo" />

            @csrf
            @method('PUT')
            <div class="w-full flex ">
                <x-button theme="primary" icon="fas fa-paper-plane" type="submit" class="w-full md:w-6/12">
                    Edit
                </x-button>
            </div>
        </form>
    </div>
</div>