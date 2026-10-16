<div class="flex flex-wrap items-center gap-2">
    <span @class(['text-sm font-medium', 'text-muted-foreground' => $status !== \App\Enums\AcademicStructureStatus::Active])>{{ $status->label() }}</span>

    @if ($canActivate)
        <april:button type="button" class="h-11 select-none" wire:click="activate" wire:loading.attr="disabled" wire:target="activate">Activate</april:button>
    @endif

    @if ($canArchive)
        <april:dropdown-menu>
            <slot:trigger>
                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More status actions">
                    <x-lucide-ellipsis class="size-4" />
                </april:button>
            </slot:trigger>
            <slot:content align="end" class="w-44">
                <april:dropdown-menu-item class="text-destructive" wire:click="archive" wire:confirm="{{ $archiveWarning }}"><x-lucide-archive class="mr-2 size-4" />Archive</april:dropdown-menu-item>
            </slot:content>
        </april:dropdown-menu>
    @endif
</div>
