<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit class {{$myClass->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('classes.update', $myClass->id)}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <x-input id="edit-class-name" name="name" label="Class Name" placeholder="Enter class name" fgroup-class="" value="{{$myClass->name}}"/> 
            <x-select id="edit-class-class_group_id" name="class_group_id" class=" m-2" label="Class group">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}" @selected(old('class_group_id') ? $classGroup->id == old('class_group_id') : $classGroup->id == $myClass->class_group_id)
                    >{{$classGroup->name}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            @method('PUT')
            <x-button label="Edit" class="w-full md:w-6/12" theme="primary" icon="fas fa-key" type="submit"/>
        </form>
    </div>
</div>

