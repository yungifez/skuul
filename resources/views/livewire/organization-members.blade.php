<div class="space-y-6">
    <april:card>
        <slot:title>Give organization scope</slot:title>
        <slot:description>
            Organization scope opens the organization screens only. It does not open campus records, and it does not
            change the schools a person already works in.
        </slot:description>
        <slot:content>
            <form wire:submit="grant" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                <div class="flex-1">
                    <april:input
                        type="email"
                        wire:model="email"
                        id="member-email"
                        placeholder="person@example.com"
                        aria-label="Email address"
                        class="w-full" />
                    @error('email')
                        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <april:button type="submit">
                    <x-lucide-user-plus class="mr-2 size-4" />
                    Give scope
                </april:button>
            </form>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Organization administrators</slot:title>
        <slot:description>{{ $activeMemberships->count() }} people administer {{ $organization->name }}.</slot:description>
        <slot:content class="space-y-4">
            @error('members')
                <april:alert variant="destructive">{{ $message }}</april:alert>
            @enderror

            @forelse ($activeMemberships as $membership)
                <div class="rounded-lg border p-4" wire:key="member-{{ $membership->id }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="font-semibold">{{ $membership->user->name }}</p>
                            <p class="text-sm text-muted-foreground">{{ $membership->user->email }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $membership->user->campus_count }} campus memberships. Removing organization
                                scope leaves those alone.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($membership->hasFullAuthority())
                                <april:badge>Organization Administrator</april:badge>
                            @else
                                <april:badge variant="secondary">Delegated</april:badge>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($membership->grantedPermissions() as $permission)
                            <span wire:key="member-{{ $membership->id }}-perm-{{ $permission->value }}">
                                <april:badge variant="outline">{{ $permission->label() }}</april:badge>
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <april:button type="button" variant="outline" size="sm" wire:click="edit({{ $membership->user_id }})">
                            <x-lucide-sliders-horizontal class="mr-2 size-3.5" />
                            Permissions
                        </april:button>

                        @if ($confirmingRemovalUserId === $membership->user_id)
                            <april:button type="button" variant="destructive" size="sm" wire:click="revoke({{ $membership->user_id }})">
                                Confirm removal
                            </april:button>
                            <april:button type="button" variant="ghost" size="sm" wire:click="cancelRemoval">
                                Cancel
                            </april:button>
                        @else
                            <april:button type="button" variant="outline" size="sm" wire:click="confirmRemoval({{ $membership->user_id }})">
                                <x-lucide-user-minus class="mr-2 size-3.5" />
                                Remove scope
                            </april:button>
                        @endif
                    </div>

                    @if ($editingUserId === $membership->user_id)
                        <div class="mt-4 space-y-4 rounded-md border bg-muted/40 p-4">
                            <p class="text-sm font-medium">What may {{ $membership->user->name }} run?</p>

                            <label class="flex items-start gap-3 text-sm">
                                <input
                                    type="checkbox"
                                    wire:model.live="fullAuthority"
                                    id="full-authority-{{ $membership->id }}"
                                    @checked($fullAuthority)
                                    class="mt-0.5 size-4 shrink-0 cursor-pointer accent-primary" />
                                <span>
                                    <span class="font-medium">Full authority</span>
                                    <span class="block text-muted-foreground">Every organization permission, including future ones.</span>
                                </span>
                            </label>

                            @unless ($fullAuthority)
                                <div class="space-y-3 border-t pt-3">
                                    @foreach ($this->delegablePermissions as $permission)
                                        <label class="flex items-start gap-3 text-sm" wire:key="draft-{{ $membership->id }}-{{ $permission->value }}">
                                            <input
                                                type="checkbox"
                                                wire:model="draftPermissions"
                                                value="{{ $permission->value }}"
                                                @checked(in_array($permission->value, $draftPermissions, true))
                                                class="mt-0.5 size-4 shrink-0 cursor-pointer accent-primary" />
                                            <span>
                                                <span class="font-medium">{{ $permission->label() }}</span>
                                                <span class="block text-muted-foreground">{{ $permission->description() }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                    <p class="text-xs text-muted-foreground">
                                        Every member keeps the read permission, so the organization screens still open.
                                    </p>
                                </div>
                            @endunless

                            @error('draftPermissions')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror

                            <div class="flex flex-wrap gap-2">
                                <april:button type="button" size="sm" wire:click="savePermissions">Save permissions</april:button>
                                <april:button type="button" variant="ghost" size="sm" wire:click="stopEditing">Cancel</april:button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted-foreground">Nobody administers this organization yet.</p>
            @endforelse
        </slot:content>
    </april:card>

    @if ($pastMemberships->isNotEmpty())
        <april:card>
            <slot:title>Past administrators</slot:title>
            <slot:description>Scope that was taken away. The record is kept so the history stays readable.</slot:description>
            <slot:content class="space-y-2">
                @foreach ($pastMemberships as $membership)
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-3" wire:key="past-{{ $membership->id }}">
                        <div>
                            <p class="font-medium">{{ $membership->user->name }}</p>
                            <p class="text-sm text-muted-foreground">{{ $membership->user->email }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <april:badge variant="outline">{{ ucfirst($membership->status->value) }}</april:badge>
                            <span class="text-sm text-muted-foreground">
                                {{ $membership->ended_at?->toFormattedDateString() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </slot:content>
        </april:card>
    @endif
</div>
