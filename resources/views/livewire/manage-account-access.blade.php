<div class="flex items-center gap-1">
    <span @class([
        'text-sm font-medium',
        'text-destructive' => $status === \App\Enums\AccountStatus::Suspended,
        'text-muted-foreground' => in_array($status, [\App\Enums\AccountStatus::Archived, \App\Enums\AccountStatus::Invited], true),
    ]) id="account-status">{{ $status->label() }}</span>

    @if ($canManage)
        <april:dropdown-menu>
            <slot:trigger>
                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Manage {{ $user->name }}'s account">
                    <x-lucide-ellipsis class="size-4" />
                </april:button>
            </slot:trigger>
            <slot:content align="start" class="w-52">
                @if ($status !== \App\Enums\AccountStatus::Archived)
                    <april:dropdown-menu-item wire:click="sendInvitation"><x-lucide-send class="mr-2 size-4" />{{ $status === \App\Enums\AccountStatus::Invited ? 'Resend invitation' : 'Send invitation' }}</april:dropdown-menu-item>
                @endif
                @if ($hasPendingInvitation)
                    <april:dropdown-menu-item wire:click="revokeInvitation" wire:confirm="Revoke the invitation for {{ $user->name }}? They can be invited again later."><x-lucide-ban class="mr-2 size-4" />Revoke invitation</april:dropdown-menu-item>
                @endif
                @if ($isBlocked)
                    <april:dropdown-menu-item wire:click="reinstate"><x-lucide-lock-open class="mr-2 size-4" />Reinstate account</april:dropdown-menu-item>
                @else
                    <april:dropdown-menu-item wire:click="suspend" wire:confirm="Suspend {{ $user->name }}? They are signed out and cannot sign in until reinstated."><x-lucide-lock class="mr-2 size-4" />Suspend account</april:dropdown-menu-item>
                    <april:dropdown-menu-item class="text-destructive" wire:click="archive" wire:confirm="Archive {{ $user->name }}? They cannot sign in until reinstated."><x-lucide-archive class="mr-2 size-4" />Archive account</april:dropdown-menu-item>
                @endif
            </slot:content>
        </april:dropdown-menu>
    @endif
</div>
