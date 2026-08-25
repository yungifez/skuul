<div class="space-y-6">
    <april:card>
        <slot:title>Students other campuses want to send here</slot:title>
        <slot:description>Approving moves the student to {{ $campusName }} straight away. Their enrollment, admission number, and placement history come with them.</slot:description>
        <slot:content>
            @if ($incoming->isEmpty())
                <p class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground">No campus is waiting on a decision from you.</p>
            @else
                <div class="space-y-4">
                    @foreach ($incoming as $request)
                        <div wire:key="incoming-{{ $request->id }}" class="rounded-md border p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">{{ $request->studentRecord?->user?->name ?? 'Unknown student' }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        From {{ $request->fromSchool?->name }}
                                        @if ($request->academicCycleSection)
                                            · into {{ $request->academicCycleSection->academicLevel?->label ?? $request->academicCycleSection->academicLevel?->name }}
                                            {{ $request->academicCycleSection->label ?? $request->academicCycleSection->name }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Asked by {{ $request->requestedBy?->name ?? 'Somebody who left' }}
                                        · effective {{ $request->effective_on?->format('M j, Y') }}
                                    </p>
                                    @if ($request->reason)
                                        <p class="mt-2 text-sm">“{{ $request->reason }}”</p>
                                    @endif
                                </div>
                                <span class="rounded-full border px-3 py-1 text-xs">{{ $request->status->label() }}</span>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                                <div class="flex flex-col gap-2">
                                    <april:label for="incoming-note-{{ $request->id }}">Note</april:label>
                                    <input id="incoming-note-{{ $request->id }}" type="text" wire:model="notes.{{ $request->id }}" placeholder="Optional note for the other campus" class="h-10 rounded-md border border-input bg-background px-3 text-sm" />
                                </div>
                                <april:button type="button" wire:click="approve({{ $request->id }})" wire:loading.attr="disabled">
                                    <x-lucide-check class="mr-2 size-4" />
                                    Approve and move
                                </april:button>
                                <april:button type="button" variant="destructive" wire:click="reject({{ $request->id }})" wire:loading.attr="disabled">
                                    <x-lucide-x class="mr-2 size-4" />
                                    Reject
                                </april:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Students this campus asked to send away</slot:title>
        <slot:description>The receiving campus decides. Until they do, the student stays here.</slot:description>
        <slot:content>
            @if ($outgoing->isEmpty())
                <p class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground">This campus has not asked to move anybody.</p>
            @else
                <div class="space-y-4">
                    @foreach ($outgoing as $request)
                        <div wire:key="outgoing-{{ $request->id }}" class="rounded-md border p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">{{ $request->studentRecord?->user?->name ?? 'Unknown student' }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        To {{ $request->toSchool?->name }}
                                        · effective {{ $request->effective_on?->format('M j, Y') }}
                                    </p>
                                </div>
                                <april:button type="button" variant="destructive" wire:click="cancel({{ $request->id }})" wire:loading.attr="disabled">
                                    <x-lucide-undo-2 class="mr-2 size-4" />
                                    Take back
                                </april:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </slot:content>
    </april:card>
</div>
