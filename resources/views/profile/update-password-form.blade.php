<x-partials.form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <x-action-message on="saved">
            {{ __('Updated password') }}
        </x-action-message>

        <div class="grid gap-4">
            <april:input-group id="current_password" type="password" wire:model="state.current_password" autocomplete="current-password" name="current_password" label="Current password" />
            <april:input-group label="New password" name="password" id="password" type="password" wire:model="state.password" autocomplete="new-password" />
            <april:input-group name="password_confirmation" label="Confirm new password" id="password_confirmation" type="password" wire:model="state.password_confirmation" autocomplete="new-password" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <april:button>
            {{ __('Save') }}
        </april:button>
    </x-slot>
</x-partials.form-section>
