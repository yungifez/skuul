<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit subject {{$subject->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.update', $subject->id)}}" method="POST" class="md:w-6/12">  
        <x-display-validation-errors/>
            <x-input id="name" name="name" label="Subject Name" placeholder="Enter subject name" value="{{$subject->name}}"/>
            <x-input id="short-name" name="short_name" label="Subject Short Name" placeholder="Enter subject short name" value="{{$subject->short_name}}"/>
            <x-select id="select" name="teachers[]" multiple label="Select Teachers" placeholder="Select teachers.....">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}" @selected(in_array($teacher->id, $assignedTeachersId))>{{$teacher->name}}</option>
                @endforeach
            </x-select>
            @csrf
            @method('PUT')
            <x-button label="Edit" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>
