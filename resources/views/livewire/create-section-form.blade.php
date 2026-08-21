<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create section</h3>
    </div>
    <div class="card-body">
        <form action="{{route('sections.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Section name" placeholder="Enter section name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="class_id">Choose class</april:label>
                <april:select id="class_id" name="my_class_id" fgroup-class="col-md-6 mx12">
                @foreach ($myClasses as $myClass)
                    <option value="{{$myClass->id}}">{{$myClass->name}}</option>
                @endforeach

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <div class="col-md-3">
                <april:button type="submit" class="w-full md:w-6/12">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
        </form>
    </div>
</div>