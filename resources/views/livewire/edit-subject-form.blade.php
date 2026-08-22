<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit subject {{$subject->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.update', $subject->id)}}" method="POST" class="md:w-6/12">
        <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject Name" placeholder="Enter subject name" value="{{$subject->name}}" />
            <april:input-group id="short-name" name="short_name" label="Subject Short Name" placeholder="Enter subject short name" value="{{$subject->short_name}}" />
            <p class="mb-4 rounded-md border border-border bg-muted/40 p-3 text-sm text-muted-foreground">Teacher assignments belong to dated course offerings, not the subject catalog.</p>
            @csrf
            @method('PUT')
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>
