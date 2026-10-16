<x-partials.action-section>
    <x-slot name="title">
        {{ __('Two Factor Authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add additional security to your account using two factor authentication.') }}
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex flex-col gap-1">
                    <h3 class="font-semibold">
                        @if ($this->enabled)
                            {{ __('Two-factor authentication is enabled.') }}
                        @else
                            {{ __('Two-factor authentication is not enabled.') }}
                        @endif
                    </h3>
                    <p class="text-sm leading-6 text-muted-foreground">
                        {{ __('Use an authenticator app to add a second check when you sign in.') }}
                    </p>
                </div>

                <april:badge variant="{{ $this->enabled ? 'secondary' : 'outline' }}">
                    {{ $this->enabled ? __('Enabled') : __('Not enabled') }}
                </april:badge>
            </div>

            @if ($this->enabled)
                @if ($showingQrCode)
                    <div class="flex flex-col gap-3 rounded-lg border bg-muted/20 p-4">
                        <p class="text-sm text-muted-foreground">
                            {{ __('Scan this QR code with your authenticator app to finish setup.') }}
                        </p>
                        <div class="flex justify-center rounded-md bg-background p-4">
                            {!! $this->user->twoFactorQrCodeSvg() !!}
                        </div>
                    </div>
                @endif

                @if ($showingRecoveryCodes)
                    <div class="flex flex-col gap-3 rounded-lg border bg-muted/20 p-4">
                        <p class="text-sm text-muted-foreground">
                            {{ __('Store these recovery codes in a secure password manager. Each code can recover access if your authenticator device is lost.') }}
                        </p>
                        <div class="grid gap-2 rounded-md bg-background p-4 font-mono text-sm">
                            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <div class="flex flex-wrap gap-2">
                @if (! $this->enabled)
                    <x-confirms-password wire:then="enableTwoFactorAuthentication">
                        <april:button type="button" variant="secondary" wire:loading.attr="disabled" size="sm">
                            {{ __('Enable') }}
                        </april:button>
                    </x-confirms-password>
                @else
                    @if ($showingRecoveryCodes)
                        <x-confirms-password wire:then="regenerateRecoveryCodes">
                            <april:button variant="outline" wire:loading.attr="disabled" size="sm">
                                {{ __('Regenerate recovery codes') }}
                            </april:button>
                        </x-confirms-password>
                    @else
                        <x-confirms-password wire:then="showRecoveryCodes">
                            <april:button variant="outline" size="sm">
                                {{ __('Show recovery codes') }}
                            </april:button>
                        </x-confirms-password>
                    @endif

                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <april:button variant="destructive" wire:loading.attr="disabled" size="sm">
                            {{ __('Disable') }}
                        </april:button>
                    </x-confirms-password>
                @endif
            </div>
        </div>
    </x-slot>
</x-partials.action-section>
