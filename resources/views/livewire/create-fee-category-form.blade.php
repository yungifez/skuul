<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Fee Category</h2>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        <form action="{{route('fee-categories.store')}}" class="md:w-6/12" method="POST">
            <april:input-group id="name" name="name" placeholder="Fee Category Name" label="Name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Fee Category Description" />
            </div>
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
