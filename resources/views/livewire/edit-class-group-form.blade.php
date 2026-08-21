<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit class group {{$classGroup->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('class-groups.update', $classGroup->id)}}" method="POST">
            <x-display-validation-errors />
            <april:input-group id="name" name="name" label="Name" placeholder="Enter class group name" class="md:w-6/12" value="{{$classGroup->name}}" />
            @csrf
            @method('PUT')
            <april:button type="submit">
                <x-lucide-pencil class="mr-2 size-4" />
                Save Changes
            </april:button>
        </form>
    </div>
</div>