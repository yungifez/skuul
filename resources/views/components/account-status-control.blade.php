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
        <x-dropdown button-label="Manage" button-class="h-7 px-2 text-xs">
            @if ($status !== AccountStatus::Archived)
                <form action="{{ route('users.invitation.send', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm hover:bg-accent">
                        <i class="fa fa-paper-plane text-xs" aria-hidden="true"></i>
                        {{ $status === AccountStatus::Invited ? 'Resend invitation' : 'Send invitation' }}
                    </button>
                </form>
            @endif

            @if ($user->pendingAccountInvitation() !== null)
                <form action="{{ route('users.invitation.revoke', $user->id) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button type="submit" class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm hover:bg-accent">
                        <i class="fa fa-ban text-xs" aria-hidden="true"></i>
                        Revoke invitation
                    </button>
                </form>
            @endif

            @foreach ($status === AccountStatus::Suspended || $status === AccountStatus::Archived
                ? [AccountStatus::Active->value => ['Reinstate account', 'fa fa-unlock']]
                : [AccountStatus::Suspended->value => ['Suspend account', 'fa fa-lock'], AccountStatus::Archived->value => ['Archive account', 'fa fa-box-archive']] as $value => [$text, $icon])
                <form action="{{ route('users.account-status', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="account_status" value="{{ $value }}">
                    <button type="submit" class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm hover:bg-accent">
                        <i class="{{ $icon }} text-xs" aria-hidden="true"></i>
                        {{ $text }}
                    </button>
                </form>
            @endforeach
        </x-dropdown>
    @endif
</div>
