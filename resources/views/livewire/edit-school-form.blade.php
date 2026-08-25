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
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <april:input-group id="address" name="address" type="text" placeholder="Enter street address" label="Address line 1 *" value="{{$school->address}}" autocomplete="address-line1" required />
                </div>
                <div class="sm:col-span-2">
                    <april:input-group id="address_line_2" name="address_line_2" type="text" placeholder="Apartment, suite, unit, building, floor, etc." label="Address line 2" value="{{$school->address_line_2}}" autocomplete="address-line2" />
                </div>
                <april:input-group id="city" name="city" type="text" placeholder="Enter city" label="City" value="{{$school->city}}" autocomplete="address-level2" />
                <april:input-group id="state" name="state" type="text" placeholder="Enter state or province" label="State / Province" value="{{$school->state}}" autocomplete="address-level1" />
                <april:input-group id="postal_code" name="postal_code" type="text" placeholder="Enter postal or ZIP code" label="Postal / ZIP code" value="{{$school->postal_code}}" autocomplete="postal-code" />
                <april:input-group id="country" name="country" type="text" placeholder="Enter country" label="Country" value="{{$school->country}}" autocomplete="country-name" />
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
