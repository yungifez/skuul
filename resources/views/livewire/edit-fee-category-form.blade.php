<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit {{$feeCategory->name}}</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fee-categories.update', $feeCategory->id)}}" class="md:w-6/12" method="POST">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" placeholder="Fee Category Name" label="Name" :value="$feeCategory->name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Fee Category Description">{{$feeCategory->description}}</april:textarea>
            </div>
            @method('PUT')
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
