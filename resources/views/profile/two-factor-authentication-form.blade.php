<x-partials.action-section>
    <x-slot name="title">
        {{ __('Two Factor Authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add additional security to your account using two factor authentication.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-xl md:text-2xl font-bold">
            @if ($this->enabled)
                {{ __('You have enabled two factor authentication.') }}
            @else
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <p class="mt-3">
            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="mt-3">
                    {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                </p>

                <div class="mt-3">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <p class="mt-3">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>

                <div class="dark:bg-gray-500 bg-gray-100 my-2 rounded p-3">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-3">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <april:button type="button" variant="secondary" wire:loading.attr="disabled" class="w-3/12">
                        {{ __('Enable') }}
                    </april:button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <april:button variant="outline" wire:loading.attr="disabled">
                            {{ __('Regenerate Recovery Codes') }}
                        </april:button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <april:button variant="outline">
                            {{ __('Show Recovery Codes') }}
                        </april:button>
                    </x-confirms-password>
                @endif

                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <april:button variant="destructive" wire:loading.attr="disabled">
                        {{ __('Disable') }}
                    </april:button>
                </x-confirms-password>
            @endif
        </div>
    </x-slot>
</x-partials.action-section>
