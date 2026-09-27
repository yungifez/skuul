<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Change the paper">
    @include('livewire.partials.exam-slot-fields')

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save the paper</april:button>
    </div>
</form>
