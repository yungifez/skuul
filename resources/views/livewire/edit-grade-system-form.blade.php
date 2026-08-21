<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit grade {{$grade->name}}</h2>
    </div>
    <div class="card-body">
        <form action="{{route('grade-systems.update' ,$grade->id)}}" method="post" class="md:w-1/2">
            <april:input-group id="name" name="name" label="Name" placeholder="Grade name eg A1" value="{{$grade->name}}" />
            <april:input-group id="remark" name="remark" label="Remark" placeholder="Grade remark eg Excellent" value="{{$grade->remark}}" />
            <april:input-group id="grade-from" type="number" name="grade_from" label="From" placeholder="Grade from eg 10" value="{{$grade->grade_from}}" />
            <april:input-group id="grade-till" type="number" name="grade_till" label="Till" placeholder="grade till eg 20" value="{{$grade->grade_till}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="class-group">Class Group</april:label>
                <april:select id="class-group" name="class_group_id" fgroup-class="col-md-6 mx-1" wire:model.live="classGroup">
                    @foreach ($classGroups as $classGroup)
                        <option value="{{ $classGroup->id }}">{{ $classGroup->name }}</option>
                    @endforeach
                </april:select>
                @error('class_group_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-pencil class="mr-2 size-4" />
                Edit
            </april:button>

            @csrf
            @method("PUT")
        </form>
    </div>
</div>
