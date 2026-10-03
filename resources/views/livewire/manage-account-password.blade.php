<section aria-labelledby="sign-in-heading" class="flex flex-col gap-3">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-x-3">
            <h2 id="sign-in-heading" class="text-base font-semibold">Sign-in</h2>
            <livewire:manage-account-access :user="$user" :key="'account-access-'.$user->id" />
            @if ($user->password_change_required_at !== null)
                <span class="text-sm text-muted-foreground">Must change password at next sign-in</span>
            @endif
        </div>
        @unless ($isOpen)
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="$set('isOpen', true)">Set password</april:button>
        @endunless
    </div>

    @if ($isOpen)
        <form wire:submit="save" class="flex flex-col gap-3" aria-label="Set a password for {{ $user->name }}">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="account-password" class="sr-only">New password</label>
                    <input type="password" id="account-password" wire:model="password" autocomplete="new-password" placeholder="New password" class="{{ $controlClasses }}" {{ field_error_bindings('password') }}>
                </div>
                <div>
                    <label for="account-password-confirmation" class="sr-only">Confirm password</label>
                    <input type="password" id="account-password-confirmation" wire:model="password_confirmation" autocomplete="new-password" placeholder="Confirm password" class="{{ $controlClasses }}">
                </div>
            </div>
            <x-field-error name="password" />
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label for="force-reset" class="flex min-h-11 cursor-pointer items-center gap-2 text-sm select-none">
                    <input type="checkbox" id="force-reset" wire:model="forceReset" class="size-4 rounded border-input">
                    Change it at next sign-in
                </label>
                <div class="flex flex-col-reverse gap-3 sm:flex-row">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('isOpen', false)">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Set password</april:button>
                </div>
            </div>
        </form>
    @endif
</section>
