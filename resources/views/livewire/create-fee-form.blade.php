<div class="card">
    <div class="card-header">
        <h2 class="card-titile">Create Fee</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fees.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Name" placeholder="Fee Name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Fee Description" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="fee-category">Fee Category</april:label>
                <april:select id="fee-category" name="fee_category_id">
                @foreach ($feeCategories as $feeCategory)
                    <option value="{{$feeCategory->id}}">{{$feeCategory->name}}</option>
                @endforeach

                </april:select>
                @error('fee_category_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
