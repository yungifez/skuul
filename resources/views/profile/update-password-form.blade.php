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
        <div class="w-md-75">
            <div class="form-group">
                <x-input id="current_password" type="password"
                             wire:model.defer="state.current_password" autocomplete="current-password" name="current_password" label="Current Password"/>
            </div>

            <div class="form-group">
                <x-input label="New Password" name="password" id="password" type="password"
                             wire:model.defer="state.password" autocomplete="new-password" />
            </div>

            <div class="form-group">
                <x-input name="password_confirmation" label="Confirm Password" id="password_confirmation" type="password"
                             wire:model.defer="state.password_confirmation" autocomplete="new-password" />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-button class="w-6/12 lg:w-3/12">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-partials.form-section>
