<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-1">
            <h3 class="card-title">Edit subject {{$subject->name}}</h3>
            <x-help-tooltip label="Subject catalog help">Teacher assignments belong to dated course offerings, not the subject catalog.</x-help-tooltip>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit="save" class="space-y-5 md:w-6/12">
        <x-display-validation-errors/>
            <april:input-group id="name" wire:model="name" label="Subject name" placeholder="Enter subject name" />
            <april:input-group id="short-name" wire:model="short_name" label="Subject short name" placeholder="Enter subject short name" />
            <april:button type="submit" wire:loading.attr="disabled" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                <span wire:loading.remove>Save changes</span>
                <span wire:loading>Saving…</span>
            </april:button>
        </form>
    </div>
</div>
