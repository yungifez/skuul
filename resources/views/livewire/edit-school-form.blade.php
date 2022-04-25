<form action="{{route('schools.update', $school->id )}}" method="POST">
    @method('PUT')
    @livewire('display-validation-error')
    <x-adminlte-input name="name" placeholder="Enter name of school" label="School Name" value="{{old('name') ? old('name') : $school->name}}"/>
    <x-adminlte-textarea name="address" placeholder="Enter school branch address" label="School Address">
        {{old('address') ? old('address') : $school->address}}
    </x-adminlte-textarea>
    <x-adminlte-input name="initials" placeholder="Enter school initials" label="School Initials" value="{{old('initials') ? old('initials') : $school->initials}}"/>   
    <x-adminlte-input name="phone" type="tel" placeholder="Enter school phone number" label="School Phone Number" value="{{old('phone') ? old('phone') : $school->phone}}"/>
    <x-adminlte-input name="email" type="email" placeholder="Enter school email" label="School Email" value="{{old('email') ? old('email') : $school->email}}"/>
    @csrf
    <x-adminlte-button label="Submit" theme="primary" icon="fas fa-paper-plane" type="submit"/>
</form>