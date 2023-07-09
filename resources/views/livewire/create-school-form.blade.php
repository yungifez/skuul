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
            <x-input name="name" id="name" type="text" placeholder="Enter name of school" label="School Name *" />
            <x-textarea id="address" name="address" placeholder="Enter school branch address" label="School Address *" />
            <x-input name="initials" id="initials" type="text" placeholder="Enter school initials" label="School initials" />
            <x-input name="phone" id="phone" placeholder="Enter school phone number" label="School Phone Number" type="tel" />
            <x-input name="email" id="email" placeholder="Enter school Email" label="School Email address" type="email" />
            <x-input name="logo" id="logo" type="file" label="Logo" />
            @csrf
            <div class="w-full flex ">
                <x-button theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-6/12">
                    Create
                </x-button>
            </div>
        </form>
    </div>
</div>
