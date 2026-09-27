<div class="flex flex-col gap-3">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $fieldId = 'period-reason-'.$period->getTable().'-'.$period->id;
    @endphp

    <div class="flex flex-wrap items-center gap-2">
        @if ($showStatus)
            <span @class(['text-sm', 'text-muted-foreground' => $status !== \App\Enums\AcademicPeriodStatus::Open])>{{ $status->label() }}</span>
        @endif

        @if ($canBeginClosing)
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="beginClosing" wire:loading.attr="disabled" wire:target="beginClosing">
                <x-lucide-lock class="mr-2 size-4" />Start closing
            </april:button>
        @endif

        @if ($canClose && $step !== 'close')
            <april:button type="button" class="h-11 select-none" wire:click="open('close')">Close</april:button>
        @endif

        @if ($canReopen && $step !== 'reopen')
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="open('reopen')">
                <x-lucide-lock-open class="mr-2 size-4" />Reopen
            </april:button>
        @endif
    </div>

    @if ($step === null)
        <x-field-error name="reason" />
    @endif

    @if (($canClose && $step === 'close') || ($canReopen && $step === 'reopen'))
        <form wire:submit="{{ $step }}" class="flex w-full max-w-md flex-col gap-3" aria-label="{{ $step === 'close' ? 'Close' : 'Reopen' }} {{ $periodName }}">
            <div>
                <label for="{{ $fieldId }}" class="sr-only">{{ $step === 'close' ? 'Note' : 'Why it is reopening' }}</label>
                <input id="{{ $fieldId }}" wire:model="reason" maxlength="500" placeholder="{{ $step === 'close' ? 'Note (optional)' : 'Why it is reopening' }}" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                <x-field-error name="reason" class="mt-1" />
            </div>
            @if ($step === 'close' && $isBlocked)
                <label class="flex min-h-11 cursor-pointer select-none items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="force" class="size-4 rounded border-input">
                    Close with this work still open
                </label>
            @endif
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                <april:button type="submit" :variant="$step === 'close' ? 'default' : 'outline'" class="h-11 select-none" wire:loading.attr="disabled" wire:target="{{ $step }}">{{ $step === 'close' ? 'Close' : 'Reopen' }}</april:button>
            </div>
        </form>
    @endif
</div>
