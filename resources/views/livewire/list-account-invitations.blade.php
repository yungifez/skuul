<div class="space-y-6">
    <april:card>
        <slot:title>Account invitations</slot:title>
        <slot:description>
            An invitation is a one-time link that lets a person set a password. One person has one sign-in account,
            so a new invitation never makes a second account, and no account is given a default password.
        </slot:description>
        <slot:content class="space-y-4">
            @if ($feedback)
                <april:alert dismissOnTimeout="true">
                    <slot:icon><x-lucide-check class="size-4" /></slot:icon>
                    <slot:title>Done</slot:title>
                    <slot:description>{{ $feedback }}</slot:description>
                </april:alert>
            @endif

            @error('invitations')
                <april:alert variant="destructive">
                    <slot:icon><x-lucide-ban class="size-4" /></slot:icon>
                    <slot:title>Not done</slot:title>
                    <slot:description>{{ $message }}</slot:description>
                </april:alert>
            @enderror

            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="-mx-1 overflow-x-auto px-1 pb-1">
                    <div role="tablist" aria-label="Invitation status" class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground">
                        @foreach ($tabs as $tab)
                            <button
                                type="button"
                                role="tab"
                                id="invitation-tab-{{ $tab->value }}"
                                aria-selected="{{ $this->currentStatus() === $tab ? 'true' : 'false' }}"
                                wire:click="selectStatus('{{ $tab->value }}')"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {{ $this->currentStatus() === $tab ? 'bg-background text-foreground shadow-sm' : 'hover:text-foreground' }}">
                                <x-icon :name="'lucide-'.$tab->icon()" class="size-3.5" />
                                {{ $tab->label() }}
                                <span class="rounded-full bg-muted-foreground/10 px-1.5 text-xs">{{ $counts[$tab->value] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="lg:w-72">
                    <april:input
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        id="invitation-search"
                        placeholder="Search name or email"
                        aria-label="Search invitations by name or email"
                        class="w-full" />
                </div>
            </div>

            <p class="text-sm text-muted-foreground" aria-live="polite">{{ $this->currentStatus()->description() }}</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>{{ $this->currentStatus()->label() }} invitations</slot:title>
        <slot:description>{{ $invitations->total() }} invitations in this state.</slot:description>
        <slot:content class="space-y-4">
            @forelse ($rows as $row)
                <div class="rounded-lg border p-4" wire:key="invitation-{{ $row['id'] }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="font-semibold">{{ $row['name'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $row['email'] }}</p>
                        </div>
                        <span>
                            <april:badge variant="{{ $row['status']->badgeVariant() }}">{{ $row['status']->label() }}</april:badge>
                        </span>
                    </div>

                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                    <dt class="text-xs font-medium uppercase text-muted-foreground">Invited by</dt>
                            <dd class="mt-1 text-sm">{{ $row['inviter'] }}</dd>
                        </div>
                        <div>
                    <dt class="text-xs font-medium uppercase text-muted-foreground">Created</dt>
                            <dd class="mt-1 text-sm">{{ $row['created_at'] }}</dd>
                        </div>
                        <div>
                    <dt class="text-xs font-medium uppercase text-muted-foreground">Expires</dt>
                            <dd class="mt-1 text-sm">{{ $row['expires_at'] }}</dd>
                        </div>
                        <div>
                    <dt class="text-xs font-medium uppercase text-muted-foreground">School memberships</dt>
                            <dd class="mt-1 flex flex-wrap gap-1.5">
                                @forelse ($row['schools'] as $school)
                                    <span wire:key="invitation-{{ $row['id'] }}-school-{{ $loop->index }}">
                                        <april:badge variant="outline">{{ $school }}</april:badge>
                                    </span>
                                @empty
                                    <span class="text-sm text-muted-foreground">No school membership</span>
                                @endforelse
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if ($row['can_resend'])
                            <april:button type="button" variant="outline" size="sm" wire:click="resend({{ $row['id'] }})" wire:loading.attr="disabled">
                                <x-lucide-send class="mr-2 size-3.5" />
                                Resend invitation
                            </april:button>
                        @else
                            <april:button type="button" variant="outline" size="sm" disabled aria-describedby="invitation-reason-{{ $row['id'] }}">
                                <x-lucide-send class="mr-2 size-3.5" />
                                Resend invitation
                            </april:button>
                        @endif

                        @if ($row['can_revoke'])
                            <april:button type="button" variant="destructive" size="sm" wire:click="revoke({{ $row['id'] }})" wire:loading.attr="disabled">
                                <x-lucide-ban class="mr-2 size-3.5" />
                                Revoke invitation
                            </april:button>
                        @else
                            <april:button type="button" variant="destructive" size="sm" disabled aria-describedby="invitation-reason-{{ $row['id'] }}">
                                <x-lucide-ban class="mr-2 size-3.5" />
                                Revoke invitation
                            </april:button>
                        @endif
                    </div>

                    @if ($row['reason'])
                        <p id="invitation-reason-{{ $row['id'] }}" class="mt-2 flex items-start gap-2 text-sm text-muted-foreground">
                            <x-lucide-info class="mt-0.5 size-3.5 shrink-0" />
                            {{ $row['reason'] }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="text-muted-foreground">No {{ strtolower($this->currentStatus()->label()) }} invitations to show.</p>
            @endforelse

            {{ $invitations->links('components.datatable-pagination-links-view') }}
        </slot:content>
    </april:card>
</div>
