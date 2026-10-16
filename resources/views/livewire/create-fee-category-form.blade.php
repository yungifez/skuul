<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Fee Category</h2>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        <form wire:submit="save" class="space-y-5 md:w-6/12">
            <april:input-group id="name" wire:model="name" placeholder="Fee Category Name" label="Name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" wire:model="description" placeholder="Fee Category Description" />
            </div>
            <x-display-validation-errors />
            <april:button type="submit" wire:loading.attr="disabled" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                <span wire:loading.remove>Create</span>
                <span wire:loading>Creating…</span>
            </april:button>
        </form>
    </div>
</div>
