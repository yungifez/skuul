<div class="space-y-8">
    <section aria-labelledby="request-inbox-heading" class="space-y-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="request-inbox-heading" class="text-lg font-semibold">Requests</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ $waitingCount }} {{ \Illuminate\Support\Str::plural('request', $waitingCount) }} waiting for the school
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-[minmax(10rem,1fr)_minmax(10rem,1fr)_auto] sm:items-end">
                <div class="space-y-1.5">
                    <label for="request-status-filter" class="text-sm font-medium">Status</label>
                    <select id="request-status-filter" wire:model.live="status" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $requestStatus)
                            <option wire:key="status-filter-{{ $requestStatus->value }}" value="{{ $requestStatus->value }}">{{ $requestStatus->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="request-type-filter" class="text-sm font-medium">Request type</label>
                    <select id="request-type-filter" wire:model.live="type" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">All types</option>
                        @foreach ($types as $requestType)
                            <option wire:key="type-filter-{{ $requestType->value }}" value="{{ $requestType->value }}">{{ $requestType->label() }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedStatus || $selectedType)
                    <april:button type="button" variant="outline" wire:click="clearFilters" wire:loading.attr="disabled">
                        Clear filters
                    </april:button>
                @endif
            </div>
        </div>

        <p wire:loading class="text-sm text-muted-foreground" role="status" aria-live="polite">Updating requests…</p>
    </section>

    <x-field-error name="status" />

    <section aria-label="Family requests" class="space-y-4" wire:loading.class="opacity-60" wire:target="status,type,clearFilters,changeStatus">
        @forelse ($requests as $request)
            <article wire:key="portal-request-{{ $request->id }}" class="min-w-0 rounded-lg border bg-card p-4 sm:p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1 space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="min-w-0 break-words text-base font-semibold">{{ $request->subject }}</h3>
                            <april:badge variant="outline">{{ $request->status->label() }}</april:badge>
                            <span class="text-sm text-muted-foreground">{{ $request->type->label() }}</span>
                        </div>

                        <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                            <div class="min-w-0">
                                <dt class="text-muted-foreground">Student</dt>
                                <dd class="mt-0.5 break-words font-medium">{{ $request->studentRecord->user->name }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-muted-foreground">From family</dt>
                                <dd class="mt-0.5 break-words font-medium">{{ $request->requestedBy->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Received</dt>
                                <dd class="mt-0.5 font-medium">{{ $request->created_at->format('M j, Y') }}</dd>
                            </div>
                        </dl>

                        @if (filled($request->message))
                            <p class="whitespace-pre-line break-words text-sm leading-6 text-foreground">{{ $request->message }}</p>
                        @endif

                        @if ($request->response)
                            <div class="rounded-md bg-muted/50 px-3 py-2.5 text-sm">
                                <p class="font-medium">School response</p>
                                <p class="mt-1 whitespace-pre-line break-words leading-6 text-muted-foreground">{{ $request->response }}</p>
                                @if ($request->answeredBy)
                                    <p class="mt-2 text-xs text-muted-foreground">{{ $request->answeredBy->name }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if ($request->status->isOpen())
                        <form wire:submit="changeStatus({{ $request->id }})" class="grid w-full gap-3 border-t pt-4 lg:w-72 lg:shrink-0 lg:border-l lg:border-t-0 lg:pl-5 lg:pt-0">
                            <div class="space-y-1.5">
                                <label for="request-{{ $request->id }}-status" class="text-sm font-medium">Move to</label>
                                <select id="request-{{ $request->id }}-status" wire:model.live="statusesByRequest.{{ $request->id }}" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('statusesByRequest.'.$request->id) }}>
                                    @foreach ($request->status->allowedNext() as $requestStatus)
                                        <option wire:key="request-{{ $request->id }}-status-{{ $requestStatus->value }}" value="{{ $requestStatus->value }}" @selected(($statusesByRequest[$request->id] ?? $request->status->allowedNext()[0]->value) === $requestStatus->value)>{{ $requestStatus->label() }}</option>
                                    @endforeach
                                </select>
                                <x-field-error :name="'statusesByRequest.'.$request->id" />
                            </div>

                            @if (($statusesByRequest[$request->id] ?? $request->status->value) === \App\Enums\PortalRequestStatus::Answered->value)
                                <div class="space-y-1.5">
                                    <label for="request-{{ $request->id }}-response" class="text-sm font-medium">Response</label>
                                    <textarea id="request-{{ $request->id }}-response" wire:model="responsesByRequest.{{ $request->id }}" rows="3" maxlength="2000" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm leading-5" {{ field_error_bindings('responsesByRequest.'.$request->id) }}></textarea>
                                    <x-field-error :name="'responsesByRequest.'.$request->id" />
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-3">
                                <april:button type="submit" wire:loading.attr="disabled" wire:target="changeStatus({{ $request->id }})">
                                    <span wire:loading.remove wire:target="changeStatus({{ $request->id }})">Update request</span>
                                    <span wire:loading wire:target="changeStatus({{ $request->id }})">Saving…</span>
                                </april:button>
                            </div>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed px-5 py-10 text-center">
                <h3 class="font-medium">{{ $selectedStatus || $selectedType ? 'No requests match these filters' : 'No requests yet' }}</h3>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ $selectedStatus || $selectedType ? 'Try another status or request type.' : 'Family requests will appear here when they are sent.' }}
                </p>
                @if ($selectedStatus || $selectedType)
                    <april:button class="mt-4" type="button" variant="outline" wire:click="clearFilters">Show all requests</april:button>
                @endif
            </div>
        @endforelse

        @if ($requests->hasPages())
            <nav aria-label="Request pages">{{ $requests->links() }}</nav>
        @endif
    </section>

    @if ($feedback)
        <p class="text-sm text-muted-foreground" role="status" aria-live="polite">{{ $feedback }}</p>
    @endif
</div>
