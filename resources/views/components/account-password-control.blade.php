@props(['user'])

<april:card>
    <slot:title>Sign-in access</slot:title>
    <slot:description>Set a password for this account without seeing the current password.</slot:description>
    <slot:content>
        <form method="POST" action="{{ route('users.password.update', $user) }}" class="grid gap-4">
            @csrf

            @if ($user->password_change_required_at !== null)
                <april:badge variant="outline">Password change required</april:badge>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <april:input-group name="password" id="account-password" type="password" label="New password" autocomplete="new-password" required />
                <april:input-group name="password_confirmation" id="account-password-confirmation" type="password" label="Confirm password" autocomplete="new-password" required />
            </div>

            @error('password')
                <p class="text-sm text-destructive">{{ $message }}</p>
            @enderror

            <label for="force-reset" class="flex items-start gap-3 text-sm">
                <input type="checkbox" id="force-reset" name="force_reset" value="1" class="mt-0.5 size-4 rounded border-input accent-primary" @checked(old('force_reset', $user->password_change_required_at !== null))>
                <span>
                    <span class="font-medium">Require a password change at next sign-in</span>
                    <span class="mt-1 block text-muted-foreground">The person can use the temporary password once, then must save a new one from their profile.</span>
                </span>
            </label>

            <div>
                <april:button type="submit">Set password</april:button>
            </div>
        </form>
    </slot:content>
</april:card>
