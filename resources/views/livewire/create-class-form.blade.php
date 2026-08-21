<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Class</h3>
    </div>
    <div class="card-body">
        <form action="{{route('classes.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="class-name-field" name="name" label="Class Name" placeholder="Enter class name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="Class-group-select">Class Group</april:label>
                <april:select id="Class-group-select" name="class_group_id">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}">{{$classGroup->name}}</option>
                @endforeach

                </april:select>
                @error('class_group_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-full my-7 md:w-6/12">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
