<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create School</h2>
    </div>
    <div class="card-body">
        <form action="{{route('schools.store')}}" method="POST" class="md:w-6/12" enctype="multipart/form-data">
            <x-display-validation-errors />
            <p class="">
                {{__('All fields marked * are required')}}
            </p>
            <div class="flex w-full flex-col gap-2">
                <april:label for="organization_id">Organization *</april:label>
                <april:select name="organization_id" id="organization_id" required>
                    <option value="">Select organization</option>
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>{{ $organization->name }}</option>
                    @endforeach
                </april:select>
            </div>
            <april:input-group name="name" id="name" type="text" placeholder="Enter name of school" label="School Name *" />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <april:input-group id="address" name="address" type="text" placeholder="Enter street address" label="Address line 1 *" autocomplete="address-line1" required />
                </div>
                <div class="sm:col-span-2">
                    <april:input-group id="address_line_2" name="address_line_2" type="text" placeholder="Apartment, suite, unit, building, floor, etc." label="Address line 2" autocomplete="address-line2" />
                </div>
                <april:input-group id="city" name="city" type="text" placeholder="Enter city" label="City" autocomplete="address-level2" />
                <april:input-group id="state" name="state" type="text" placeholder="Enter state or province" label="State / Province" autocomplete="address-level1" />
                <april:input-group id="postal_code" name="postal_code" type="text" placeholder="Enter postal or ZIP code" label="Postal / ZIP code" autocomplete="postal-code" />
                <april:input-group id="country" name="country" type="text" placeholder="Enter country" label="Country" autocomplete="country-name" />
            </div>
            <april:input-group name="initials" id="initials" type="text" placeholder="Enter school initials" label="School initials" />
            <april:input-group name="phone" id="phone" placeholder="Enter school phone number" label="School Phone Number" type="tel" />
            <april:input-group name="email" id="email" placeholder="Enter school Email" label="School Email address" type="email" />
            <april:input-group name="logo" id="logo" type="file" label="Logo" />
            @csrf
            <div class="w-full flex ">
                <april:button type="submit" class="w-full md:w-6/12">
<x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
        </form>
    </div>
</div>
