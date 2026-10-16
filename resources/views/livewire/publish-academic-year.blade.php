<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
    @error('setup')
        <p class="text-sm text-destructive" role="alert">{{ $message }}</p>
    @enderror
    <april:button type="button" wire:click="publish" wire:loading.attr="disabled">Publish and finish setup</april:button>
</div>
