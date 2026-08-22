<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create subject</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject Name" placeholder="Enter subject name" />
            <april:input-group id="short-name" name="short_name" label="Subject short Name" placeholder="Enter subject short name" />
            <p class="rounded-md border border-border bg-muted/40 p-3 text-sm text-muted-foreground">Subjects are part of the school catalog. Choose where and when they are taught, then assign teachers, from a course offering.</p>
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
