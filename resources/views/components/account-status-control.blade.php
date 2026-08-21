@props(['user'])

@php
    use App\Enums\AccountStatus;

    $status = $user->account_status;

    $variant = match ($status) {
        AccountStatus::Active    => 'default',
        AccountStatus::Invited   => 'secondary',
        AccountStatus::Suspended => 'destructive',
        AccountStatus::Archived  => 'outline',
    };

    $canManage = auth()->user()->can('manageAccountAccess', $user);
@endphp

<div class="flex items-center gap-2">
    <april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>

    @if ($canManage)
        <april:dropdown-menu>
            <slot:trigger>
                <april:button variant="outline" size="sm" class="h-7 px-2 text-xs" type="button" aria-haspopup="true">
                    Manage
                    <x-lucide-chevron-down class="size-3.5" />
                </april:button>
            </slot:trigger>
            <slot:content class="min-w-40 p-1">
            @if ($status !== AccountStatus::Archived)
                <form action="{{ route('users.invitation.send', $user->id) }}" method="POST">
                    @csrf
                    <april:button type="submit" variant="ghost" class="w-full justify-start gap-2 px-3 py-2 text-left text-sm">
                        <x-lucide-send class="size-3.5"  />
                        {{ $status === AccountStatus::Invited ? 'Resend invitation' : 'Send invitation' }}
                    </april:button>
                </form>
            @endif

            @if ($user->pendingAccountInvitation() !== null)
                <form action="{{ route('users.invitation.revoke', $user->id) }}" method="POST">
                    @csrf
                    @method('delete')
                    <april:button type="submit" variant="ghost" class="w-full justify-start gap-2 px-3 py-2 text-left text-sm">
                        <x-lucide-ban class="size-3.5"  />
                        Revoke invitation
                    </april:button>
                </form>
            @endif

            @foreach ($status === AccountStatus::Suspended || $status === AccountStatus::Archived
                ? [AccountStatus::Active->value => ['Reinstate account', 'unlock']]
                : [AccountStatus::Suspended->value => ['Suspend account', 'lock'], AccountStatus::Archived->value => ['Archive account', 'archive']] as $value => [$text, $icon])
                <form action="{{ route('users.account-status', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="account_status" value="{{ $value }}">
                    <april:button type="submit" variant="ghost" class="w-full justify-start gap-2 px-3 py-2 text-left text-sm">
                        <x-icon :name="'lucide-'.$icon" class="size-3.5" />
                        {{ $text }}
                    </april:button>
                </form>
            @endforeach
            </slot:content>
        </april:dropdown-menu>
    @endif
</div>
