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
            <april:input-group id="name" name="name" placeholder="Enter name of school" label="School Name *" value="{{$school->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="address">School Address *</april:label>
                <april:textarea id="address" name="address" placeholder="Enter school branch address">{{$school->address}}</april:textarea>
            </div>
            <april:input-group id="initials" name="initials" placeholder="Enter school initials" label="School Initials" value="{{$school->initials}}" />
            <april:input-group id="phone" name="phone" type="tel" placeholder="Enter school phone number" label="School Phone Number" value="{{ $school->phone}}" />
            <april:input-group id="email" name="email" type="email" placeholder="Enter school email" label="School Email" value="{{ $school->email}}" />
            <april:input-group name="logo" id="logo" type="file" label="Logo" />

            @csrf
            @method('PUT')
            <div class="w-full flex ">
                <april:button type="submit" class="w-full md:w-6/12">
<x-lucide-send class="mr-2 size-4" />
                    Edit
                </april:button>
            </div>
        </form>
    </div>
</div>
