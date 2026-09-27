<div class="flex flex-wrap items-center gap-3 border-t pt-4">
    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
        {{ $batch->status->label() }}
    </span>

    @can('apply', $batch)
        @if ($batch->status->canBeApplied())
            @if ($batch->valid_count > 0)
                <april:button type="button" wire:click="apply" class="h-11 select-none" wire:loading.attr="disabled" wire:target="apply,cancel">
                    <x-lucide-database-backup class="mr-2 size-4" />
                    Write {{ $batch->valid_count }} {{ Str::plural('row', $batch->valid_count) }}
                </april:button>
            @else
                <span class="text-sm text-muted-foreground">No row passed the check, so there is nothing to write.</span>
            @endif

            <april:button type="button" variant="outline" wire:click="cancel" wire:confirm="Drop this import? Nothing in it will be written." class="h-11 select-none" wire:loading.attr="disabled" wire:target="apply,cancel">
                Drop this import
            </april:button>
        @elseif ($batch->applied_at !== null)
            <span class="text-sm text-muted-foreground">
                Written on {{ $batch->applied_at->format('j M Y') }}. An import runs once.
            </span>
        @else
            <span class="text-sm text-muted-foreground">This import is finished. Load the file again to run it.</span>
        @endif
    @endcan
</div>
