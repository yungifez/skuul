<form action="{{route('schools.store')}}" method="POST">
    <x-adminlte-input name="name" placeholder="Enter name of school" label="School Name" value="{{old('name')}}"/>
    <x-adminlte-textarea name="address" placeholder="Enter school branch address" label="School Address">
        {{old('address')}}
    </x-adminlte-textarea>
    <x-adminlte-input name="initials" placeholder="Enter school initials" label="School initials" value="{{old('initials')}}"/>    
    @csrf
    <x-adminlte-button label="Submit" theme="primary" icon="fas fa-paper-plane" type="submit"/>
</form>