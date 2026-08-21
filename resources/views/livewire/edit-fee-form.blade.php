<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit {{$fee->name}}</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fee-categories.update', $fee->id)}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Name" placeholder="Fee Name" value="{{$fee->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Fee Description">{{$fee->description}}</april:textarea>
            </div>
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-pencil class="mr-2 size-4" />
                Edit
            </april:button>
            @csrf
            @method('PUT')
        </form>
    </div>
</div>
