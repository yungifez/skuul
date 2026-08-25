<x-partials.action-section>
    <x-slot name="title">
        {{ __('Browser Sessions') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Manage and log out your active sessions on other browsers and devices.') }}
    </x-slot>

    <x-slot name="content">
        <x-action-message on="loggedOut">
            {{ __('Logged Out Of All Browsers.') }}
        </x-action-message>

        <p class="text-sm leading-6 text-muted-foreground">
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
        </p>

        @if (count($this->sessions) > 0)
            <div class="divide-y rounded-lg border">
                @foreach ($this->sessions as $session)
                    <div class="flex items-start gap-3 p-4">
                        <div class="shrink-0 pt-0.5 text-muted-foreground">
                            @if ($session->agent->isDesktop())
                                <x-lucide-monitor class="size-5" />
                            @else
                                <x-lucide-smartphone class="size-5" />
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-col gap-1">
                            <p class="font-medium">
                                {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                            </p>

                            <p class="text-sm text-muted-foreground">
                                {{ $session->ip_address }},

                                @if ($session->is_current_device)
                                    <span class="font-medium text-foreground">{{ __('This device') }}</span>
                                @else
                                    {{ __('Last active') }} {{ $session->last_active }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <april:dialog dismissable x-effect="show = $wire.confirmingLogout">
            <slot:content class="sm:max-w-md">
                <april:dialog-header>
                    <slot:title>Log Out Other Browser Sessions</slot:title>
                    <slot:description>{{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}</slot:description>
                </april:dialog-header>

                <div class="grid gap-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <april:input-group id="password-for-logout" name="password" type="password" placeholder="{{ __('Password') }}" label="Confirm Password to continue" x-ref="password" class="w-full" wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                </div>

                <april:dialog-footer>
                    <april:button variant="destructive" size="sm" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                        {{ __('Log out other browser sessions') }}
                    </april:button>
                </april:dialog-footer>
            </slot:content>
        </april:dialog>
    </x-slot>

</x-partials.action-section>
