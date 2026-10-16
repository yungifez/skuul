<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Change the exam">
    @include('livewire.partials.exam-fields')

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">
            Save the exam
        </april:button>
    </div>
</form>
