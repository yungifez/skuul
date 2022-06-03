<div class="card">
    <div class="card-body">
        <form action="{{route('schools.update', $school->id )}}" method="POST">
            @method('PUT')
            @livewire('display-validation-error')
            <x-adminlte-input name="name" placeholder="Enter name of school" label="School Name" value="{{$school->name}}" enable-old-support fgroup-class="col-md-6"/>
            <x-adminlte-textarea name="address" placeholder="Enter school branch address" label="School Address" enable-old-support fgroup-class="col-md-6">
                {{$school->address}}
            </x-adminlte-textarea>
            <x-adminlte-input name="initials" placeholder="Enter school initials" label="School Initials" value="{{$school->initials}}" enable-old-support fgroup-class="col-md-6"/>   
            <x-adminlte-input name="phone" type="tel" placeholder="Enter school phone number" label="School Phone Number" value="{{ $school->phone}}"  enable-old-support fgroup-class="col-md-6"/>
            <x-adminlte-input name="email" type="email" placeholder="Enter school email" label="School Email" value="{{ $school->email}}"  enable-old-support fgroup-class="col-md-6"/>
            @csrf
            <x-adminlte-button label="Submit" theme="primary" icon="fas fa-paper-plane" type="submit" class="col-md-3"/>
        </form>
    </div>
</div>