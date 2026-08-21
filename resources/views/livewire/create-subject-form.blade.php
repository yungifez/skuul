<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create subject</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject Name" placeholder="Enter subject name" />
            <april:input-group id="short-name" name="short_name" label="Subject short Name" placeholder="Enter subject short name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="class-select">Add subject to class</april:label>
                <april:select id="class-select" name="my_class_id" placeholder="Select a class...">
                @foreach ($classes as $class)
                    <option value="{{$class->id}}">{{$class->name}}</option>
                @endforeach

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="select">Select Teachers</april:label>
                <april:select id="select" name="teachers[]" multiple placeholder="Select teachers.....">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach

                </april:select>
                @error('teachers[]')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>