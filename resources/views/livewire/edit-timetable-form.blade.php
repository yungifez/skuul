<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.update',$timetable)}}" method="POST" class="md:w-1/2">
            @csrf
            @method('PUT')
            <april:input-group id="name" name="name" label="Timetable name" placeholder="Enter timetable name" value="{{$timetable->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description">{{$timetable->description}}</april:textarea>
            </div>
            <div class="rounded-md border border-border bg-muted/40 p-3 text-sm">
                <p class="font-medium">Home group</p>
                <p class="text-muted-foreground">{{ $timetable->academicCycleSection->academicLevel->label ?? $timetable->academicCycleSection->academicLevel->name }} · {{ $timetable->academicCycleSection->label ?? $timetable->academicCycleSection->name }}</p>
            </div>
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
            </div>
        </form>
    </div>
</div>
