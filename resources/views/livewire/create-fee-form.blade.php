<div class="card">
    <div class="card-header">
        <h2 class="card-titile">Create Fee</h2>
    </div>
    <div class="card-body">
        <form wire:submit="save" class="space-y-5 md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="name" wire:model="name" label="Name" placeholder="Fee Name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" wire:model="description" placeholder="Fee Description" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="fee-category">Fee Category</april:label>
                <april:select id="fee-category" wire:model="fee_category_id">
                @foreach ($feeCategories as $feeCategory)
                    <option value="{{$feeCategory->id}}">{{$feeCategory->name}}</option>
                @endforeach

                </april:select>
                <x-field-error name="fee_category_id" />
            </div>
            <april:button type="submit" wire:loading.attr="disabled" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                <span wire:loading.remove>Create</span>
                <span wire:loading>Creating…</span>
            </april:button>
        </form>
    </div>
</div>
