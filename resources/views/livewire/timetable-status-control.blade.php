<div class="flex flex-wrap items-center gap-3">
    <span @class(['text-sm font-medium', 'text-muted-foreground' => $status !== \App\Enums\TimetableStatus::Published]) id="timetable-status">
        {{ $status->label() }}@if (!$acceptsChanges && $status !== \App\Enums\TimetableStatus::Archived) · Period closed @endif
    </span>

    @if ($canPublish)
        <april:button type="button" class="h-11 select-none" wire:click="publish" wire:confirm="Publish this timetable? Published entries cannot be edited." wire:loading.attr="disabled" wire:target="publish">
            <x-lucide-send class="mr-2 size-4" />Publish
        </april:button>
    @elseif ($canRevise)
        <april:button type="button" variant="outline" class="h-11 select-none" wire:click="revise" wire:loading.attr="disabled" wire:target="revise">
            <x-lucide-copy-plus class="mr-2 size-4" />New revision
        </april:button>
    @endif
</div>
