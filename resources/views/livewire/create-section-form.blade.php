<form action="{{route('sections.store')}}" method="POST">
    <x-adminlte-input name="name" label="Section name" placeholder="Enter section name" fgroup-class="col-md-6"/>
    <x-adminlte-select name="my_class_id" class="col-md-6 m-2" >
        @foreach ($myClasses as $myClass)
            <option value="{{$myClass->id}}">{{$myClass->name}}</option>
        @endforeach
    </x-adminlte-select>
    @csrf
    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit"/>
</form>