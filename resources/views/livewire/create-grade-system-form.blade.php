<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create grade</h3>
    </div>
    <div class="card-body">
        <form action="{{route('grade-systems.store')}}" method="post" class="md:w-1/2">
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <april:input-group id="name" name="name" label="Name *" placeholder="Grade name eg A1" />
            <april:input-group id="remark" name="remark" label="Remark" placeholder="Grade remark eg Excellent" />
            <april:input-group id="grade-from" type="number" name="grade_from" label="From *" placeholder="Grade from eg 10" />
            <april:input-group id="grade-till" type="number" name="grade_till" label="Till *" placeholder="Grade till eg 20" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="class-group">Class Group *</april:label>
                <april:select id="class-group" name="class_group_id" fgroup-class="col-md-6 mx-1">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}" @selected(old('class_group_id') == $classGroup->id)>{{$classGroup->name}}</option>
                @endforeach

                </april:select>
                @error('class_group_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
            @csrf
        </form>
    </div>
</div>