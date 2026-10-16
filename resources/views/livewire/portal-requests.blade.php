<div class="mx-auto flex w-full max-w-3xl flex-col gap-10">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $learner = $studentRecord->user?->name ?? 'your child';
    @endphp

    <section aria-labelledby="ask-heading" class="flex flex-col gap-4">
        <div>
            <h2 id="ask-heading" class="text-base font-semibold">Ask the school</h2>
            <p class="text-sm text-muted-foreground">Send a message about {{ $learner }}. The school will read and answer it.</p>
        </div>

        <form wire:submit="send" class="flex flex-col gap-3" aria-label="Ask the school">
            <div class="grid gap-3 sm:grid-cols-[1fr_12rem]">
                <div>
                    <label for="request-subject" class="sr-only">What you need</label>
                    <input id="request-subject" wire:model="subject" maxlength="255" required placeholder="What you need, e.g. a copy of last term's report" class="{{ $controlClasses }}" {{ field_error_bindings('subject') }}>
                </div>
                <div>
                    <label for="request-type" class="sr-only">Kind of request</label>
                    <select id="request-type" wire:model="type" class="{{ $controlClasses }}" {{ field_error_bindings('type') }}>
                        @foreach ($types as $requestType)
                            <option value="{{ $requestType->value }}">{{ $requestType->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="request-message" class="sr-only">More detail (optional)</label>
                <textarea id="request-message" wire:model="message" rows="3" maxlength="2000" placeholder="More detail (optional)"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('message') }}></textarea>
            </div>
            <x-field-error name="subject" />
            <x-field-error name="type" />
            <x-field-error name="message" />
            <div class="flex sm:justify-end">
                <april:button type="submit" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="send">
                    <x-lucide-send class="mr-2 size-4" aria-hidden="true" />Send
                </april:button>
            </div>
        </form>
    </section>

    <section aria-labelledby="asked-heading" class="flex flex-col gap-3">
        <h2 id="asked-heading" class="text-base font-semibold">What you have asked for</h2>

        @if ($requests->isEmpty())
            <p class="text-sm text-muted-foreground">You have not asked for anything yet</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($requests as $request)
                    <li wire:key="request-{{ $request->id }}" class="flex flex-col gap-2 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium [overflow-wrap:anywhere]">{{ $request->subject }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $request->type->label() }} · sent {{ school_time($request->created_at)?->format('j M Y') }} · {{ $request->status->label() }}
                                </p>
                            </div>
                            @if ($request->status->isOpen())
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" aria-label="Actions for {{ $request->subject }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content align="end">
                                        <april:dropdown-menu-item class="text-destructive" wire:click="withdraw({{ $request->id }})" wire:confirm="Take back this request? The school will stop working on it.">
                                            <x-lucide-undo-2 class="mr-2 size-4" />Take back
                                        </april:dropdown-menu-item>
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>
                        @if (filled($request->message))
                            <p class="whitespace-pre-line text-sm [overflow-wrap:anywhere]">{{ $request->message }}</p>
                        @endif
                        @if (filled($request->response))
                            <div class="border-l-2 pl-3">
                                <p class="text-xs text-muted-foreground">The school answered on {{ school_time($request->answered_at)?->format('j M Y') ?? '—' }}</p>
                                <p class="mt-1 whitespace-pre-line text-sm [overflow-wrap:anywhere]">{{ $request->response }}</p>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
