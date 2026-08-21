<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit class {{$myClass->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('classes.update', $myClass->id)}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="edit-class-name" name="name" label="Class Name" placeholder="Enter class name" fgroup-class="" value="{{$myClass->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="edit-class-class_group_id">Class group</april:label>
                <april:select id="edit-class-class_group_id" name="class_group_id" class=" m-2">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}" @selected(old('class_group_id') ? $classGroup->id == old('class_group_id') : $classGroup->id == $myClass->class_group_id)
                    >{{$classGroup->name}}</option>
                @endforeach

                </april:select>
                @error('class_group_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            @method('PUT')
            <april:button class="w-full md:w-6/12" type="submit">
                <x-lucide-key class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>

