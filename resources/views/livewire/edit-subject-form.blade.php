<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit subject {{$subject->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.update', $subject->id)}}" method="POST" class="md:w-6/12">
        <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject Name" placeholder="Enter subject name" value="{{$subject->name}}" />
            <april:input-group id="short-name" name="short_name" label="Subject Short Name" placeholder="Enter subject short name" value="{{$subject->short_name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="select">Select Teachers</april:label>
                <april:select id="select" name="teachers[]" multiple placeholder="Select teachers.....">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}" @selected(in_array($teacher->id, $assignedTeachersId))>{{$teacher->name}}</option>
                @endforeach

                </april:select>
                @error('teachers[]')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            @method('PUT')
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>
