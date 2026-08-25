<div>
    <livewire:show-user-profile :user="$admin" />

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <april:card>
            <slot:title>School access</slot:title>
            <slot:description>Access to this school is separate from the person’s sign-in account.</slot:description>
            <slot:content>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Account</dt>
                        <dd class="mt-1"><x-account-status-control :user="$admin" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Membership</dt>
                        <dd class="mt-1">
                            @if ($membership)
                                <april:badge variant="{{ $membership->status->value === 'active' ? 'default' : 'outline' }}">
                                    {{ $membership->status->label() }}
                                </april:badge>
                            @else
                                <span class="text-sm text-muted-foreground">No membership</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Joined</dt>
                        <dd class="mt-1 text-sm">{{ $membership?->joined_at?->format('M j, Y') ?: 'Not recorded' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Primary school</dt>
                        <dd class="mt-1 text-sm">{{ $membership?->is_primary ? 'Yes' : 'No' }}</dd>
                    </div>
                </dl>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Administrator details</slot:title>
            <slot:description>Roles and invitation state for this account.</slot:description>
            <slot:content class="space-y-5">
                <div>
                    <p class="text-xs font-medium uppercase text-muted-foreground">Roles</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse ($roles as $role)
                            <april:badge variant="secondary">{{ $role }}</april:badge>
                        @empty
                            <span class="text-sm text-muted-foreground">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase text-muted-foreground">Invitation</p>
                    @if ($pendingInvitation)
                        <p class="mt-1 text-sm">Pending until {{ $pendingInvitation->expires_at->format('M j, Y') }}.</p>
                    @elseif ($admin->isAwaitingInvitationAcceptance())
                        <p class="mt-1 text-sm text-muted-foreground">This account is waiting for an invitation to be accepted.</p>
                    @else
                        <p class="mt-1 text-sm text-muted-foreground">No pending invitation.</p>
                    @endif
                </div>
            </slot:content>
        </april:card>
    </div>
</div>
