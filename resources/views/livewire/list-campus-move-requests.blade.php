<div class="flex flex-col gap-10">
    <section aria-labelledby="incoming-heading" class="flex flex-col gap-3">
        <div>
            <h2 id="incoming-heading" class="text-lg font-semibold">Students other campuses want to send here</h2>
            <p class="text-sm text-muted-foreground">Approving moves the student to {{ $campusName }} straight away. Their enrollment, admission number, and placement history come with them.</p>
        </div>

        @if ($incoming->isEmpty())
            <p class="border-y py-6 text-sm text-muted-foreground">No campus is waiting on a decision from you.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($incoming as $request)
                    @php
                        $learnerName = $request->studentRecord?->user?->name ?? 'Unknown student';
                    @endphp
                    <li wire:key="incoming-{{ $request->id }}" class="flex flex-col gap-3 py-4">
                        <div>
                            <p class="font-medium">{{ $learnerName }}</p>
                            <p class="text-sm text-muted-foreground">
                                From {{ $request->fromSchool?->name }}
                                @if ($request->academicCycleSection)
                                    · into {{ $request->academicCycleSection->academicLevel?->name }} · {{ $request->academicCycleSection->label ?? $request->academicCycleSection->name }}
                                @endif
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Asked by {{ $request->requestedBy?->name ?? 'Somebody who left' }}
                                · effective {{ $request->effective_on?->format('j M Y') }}
                            </p>
                            @if ($request->reason)
                                <p class="mt-2 text-sm">“{{ $request->reason }}”</p>
                            @endif
                        </div>

                        <div class="grid gap-3 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                            <div class="flex flex-col gap-1.5">
                                <april:label for="incoming-note-{{ $request->id }}">Note</april:label>
                                <input id="incoming-note-{{ $request->id }}" type="text" wire:model="notes.{{ $request->id }}" maxlength="500" placeholder="Optional note for the other campus" class="h-11 rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('notes.'.$request->id) }} />
                                <x-field-error :name="'notes.'.$request->id" />
                            </div>
                            <april:button type="button" class="h-11" wire:click="approve({{ $request->id }})" wire:confirm="Move {{ $learnerName }} to {{ $campusName }} now?" wire:loading.attr="disabled">
                                <x-lucide-check class="mr-2 size-4" />
                                Approve and move
                            </april:button>
                            <april:button type="button" variant="outline" class="h-11" wire:click="reject({{ $request->id }})" wire:confirm="Reject the move of {{ $learnerName }}? They stay at {{ $request->fromSchool?->name }}." wire:loading.attr="disabled">
                                <x-lucide-x class="mr-2 size-4" />
                                Reject
                            </april:button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section aria-labelledby="outgoing-heading" class="flex flex-col gap-3">
        <div>
            <h2 id="outgoing-heading" class="text-lg font-semibold">Students this campus asked to send away</h2>
            <p class="text-sm text-muted-foreground">The receiving campus decides. Until they do, the student stays here.</p>
        </div>

        @if ($outgoing->isEmpty())
            <p class="border-y py-6 text-sm text-muted-foreground">This campus has not asked to move anybody.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($outgoing as $request)
                    @php
                        $learnerName = $request->studentRecord?->user?->name ?? 'Unknown student';
                    @endphp
                    <li wire:key="outgoing-{{ $request->id }}" class="flex flex-wrap items-start justify-between gap-3 py-4">
                        <div>
                            <p class="font-medium">{{ $learnerName }}</p>
                            <p class="text-sm text-muted-foreground">
                                To {{ $request->toSchool?->name }}
                                @if ($request->academicCycleSection)
                                    · {{ $request->academicCycleSection->academicLevel?->name }} · {{ $request->academicCycleSection->label ?? $request->academicCycleSection->name }}
                                @endif
                                · effective {{ $request->effective_on?->format('j M Y') }}
                            </p>
                            @if ($request->reason)
                                <p class="mt-2 text-sm">“{{ $request->reason }}”</p>
                            @endif
                        </div>
                        <april:button type="button" variant="outline" class="h-11" wire:click="cancel({{ $request->id }})" wire:confirm="Take back the request to move {{ $learnerName }}?" wire:loading.attr="disabled">
                            <x-lucide-undo-2 class="mr-2 size-4" />
                            Take back
                        </april:button>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
