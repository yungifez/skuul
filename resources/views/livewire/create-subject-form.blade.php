<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create subject</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Subject Name" placeholder="Enter subject name" />
            <x-input id="short-name" name="short_name" label="Subject short Name" placeholder="Enter subject short name" />
            <x-select id="class-select" name="my_class_id" label="Add subject to class" placeholder="Select a class..." >
                @foreach ($classes as $class)
                    <option value="{{$class->id}}">{{$class->name}}</option>
                @endforeach
            </x-select>
            <x-select id="select" name="teachers[]" multiple label="Select Teachers" placeholder="Select teachers.....">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach
            </x-adminlte-select2>
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>