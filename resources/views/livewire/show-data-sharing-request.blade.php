<div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
    @php
        use App\Enums\DataSharingStatus;

        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $categories = collect($sharingRequest->categories())->map(fn ($category) => $category->label())->join(', ');
        $holderName = $sharingRequest->holdingSchool?->name ?? '—';
        $canApprove = in_array(DataSharingStatus::Approved, $decisions, true);
        $canDecline = in_array(DataSharingStatus::Declined, $decisions, true);
        $menuDecisions = array_values(array_filter($decisions, fn (DataSharingStatus $status): bool => !in_array($status, [DataSharingStatus::Approved, DataSharingStatus::Declined], true)));
        $menuLabels = [
            DataSharingStatus::Expired->value => 'Mark as run out',
            DataSharingStatus::Revoked->value => 'Take permission back',
        ];
        $menuWarnings = [
            DataSharingStatus::Expired->value => 'Mark this permission as run out? The records can no longer be handed over under it.',
            DataSharingStatus::Revoked->value => 'Take this permission back? The records can no longer be handed over under it.',
        ];
    @endphp

    <section aria-label="Request" class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <p class="font-medium">{{ $sharingRequest->requestingSchool?->name ?? '—' }} asked {{ $holderName }}</p>
            <p class="text-sm text-muted-foreground">{{ $sharingRequest->purpose }}</p>
            @if ($sharingRequest->hasExpired())
                <p class="text-sm text-destructive">Ran out on {{ $sharingRequest->expires_on->format('j M Y') }}</p>
            @endif
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">State</dt>
                <dd class="font-medium">{{ $sharingRequest->status->label() }}</dd>
                @if ($sharingRequest->decidedBy !== null)
                    <dd class="text-xs text-muted-foreground">{{ $sharingRequest->decidedBy->name }} · {{ $sharingRequest->decided_at?->format('j M Y') }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Learner</dt>
                <dd class="font-medium">{{ $sharingRequest->studentRecord?->admission_number ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Asked on</dt>
                <dd class="font-medium">{{ $sharingRequest->created_at->format('j M Y') }}</dd>
                <dd class="text-xs text-muted-foreground">{{ $sharingRequest->requestedBy?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Runs out</dt>
                <dd class="font-medium">{{ $sharingRequest->expires_on?->format('j M Y') ?? '—' }}</dd>
            </div>
            <div class="col-span-2 sm:col-span-4">
                <dt class="text-sm text-muted-foreground">Asked for</dt>
                <dd class="font-medium">{{ $categories !== '' ? $categories : '—' }}</dd>
            </div>
            @if (filled($sharingRequest->decision_note))
                <div class="col-span-2 sm:col-span-4">
                    <dt class="text-sm text-muted-foreground">Note from {{ $holderName }}</dt>
                    <dd>{{ $sharingRequest->decision_note }}</dd>
                </div>
            @endif
        </dl>

        @if ($isHolder && ($decisions !== [] || $canFulfil))
            <div class="flex flex-col gap-3">
                @if ($decisions !== [])
                    <div class="max-w-md">
                        <label for="decision-note" class="sr-only">Note</label>
                        <input id="decision-note" wire:model="note" maxlength="500" placeholder="Note (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('note') }}>
                        <x-field-error name="note" class="mt-1" />
                    </div>
                @endif
                <div class="flex flex-wrap items-center gap-2">
                    @if ($canFulfil)
                        <april:button type="button" class="h-11 select-none" wire:click="fulfil" wire:loading.attr="disabled" wire:target="fulfil">
                            <x-lucide-package class="mr-2 size-4" />Hand the records over
                        </april:button>
                    @endif
                    @if ($canApprove)
                        <april:button type="button" class="h-11 select-none" wire:click="decide('{{ DataSharingStatus::Approved->value }}')" wire:loading.attr="disabled" wire:target="decide">Approve</april:button>
                    @endif
                    @if ($canDecline)
                        <april:button type="button" variant="outline" class="h-11 select-none" wire:click="decide('{{ DataSharingStatus::Declined->value }}')" wire:confirm="Decline this request?" wire:loading.attr="disabled" wire:target="decide">Decline</april:button>
                    @endif
                    @if ($menuDecisions !== [])
                        <april:dropdown-menu>
                            <slot:trigger>
                                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More answers">
                                    <x-lucide-ellipsis class="size-4" />
                                </april:button>
                            </slot:trigger>
                            <slot:content align="end" class="w-56">
                                @foreach ($menuDecisions as $decision)
                                    <april:dropdown-menu-item class="text-destructive" wire:click="decide('{{ $decision->value }}')" wire:confirm="{{ $menuWarnings[$decision->value] ?? '' }}">{{ $menuLabels[$decision->value] ?? $decision->label() }}</april:dropdown-menu-item>
                                @endforeach
                            </slot:content>
                        </april:dropdown-menu>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <section aria-labelledby="records-heading" class="flex flex-col gap-4">
        <h2 id="records-heading" class="text-base font-semibold">Records</h2>
        @if ($package === null)
            <p class="text-sm text-muted-foreground">Nothing has been handed over</p>
        @else
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-3">
                <div>
                    <dt class="text-sm text-muted-foreground">Built on</dt>
                    <dd class="font-medium">{{ $package->created_at->format('j M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Kinds of record</dt>
                    <dd class="font-medium tabular-nums">{{ count($package->categories) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">Taken in</dt>
                    <dd class="font-medium">{{ $package->received_at?->format('j M Y') ?? '—' }}</dd>
                </div>
            </dl>
            @if ($isRequester && !$package->wasReceived())
                <div>
                    <april:button type="button" class="h-11 select-none" wire:click="receive" wire:loading.attr="disabled" wire:target="receive">
                        <x-lucide-download class="mr-2 size-4" />Take the records in
                    </april:button>
                </div>
            @endif
        @endif
    </section>
</div>
