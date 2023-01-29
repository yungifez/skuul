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

        <div>
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-3">
                <!-- Other Browser Sessions -->
                @foreach ($this->sessions as $session)
                    <div class="flex gap-3">
                        <div>
                            @if ($session->agent->isDesktop())
                                <svg fill="none" width="32" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="text-muted">
                                    <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                    <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
                                </svg>
                            @endif
                        </div>

                        <div class="">
                            <div>
                                {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                            </div>

                            <div>
                                <div class="small font-light">
                                    {{ $session->ip_address }},

                                    @if ($session->is_current_device)
                                        <span class="text-green-500 font-bold">{{ __('This device') }}</span>
                                    @else
                                        {{ __('Last active') }} {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <x-modal background-colour="bg-red-700" >
            <x-slot:button-text >
                Log Out Other Browser Sessions
            </x-slot:button-text>
            <x-slot:title >
                <p class="text-lg md:text-2xl">
                    Log Out Other Browser Sessions
                </p>
            </x-slot:title>

            <p class="px-4 text-center">
                {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
            </p>

            <div class="my-3" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-input id="password-for-logout" name="password" type="password" placeholder="{{ __('Password') }}"
                    label="Confirm Password to continue"
                    x-ref="password"
                    class="w-full"
                    wire:model.defer="password"
                    wire:keydown.enter="logoutOtherBrowserSessions" />
            </div>

            <x-slot name="footer">
                <x-button class="bg-red-600 text-sm px-2 md:px-4" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled" x-effect="modal = $wire.confirmingLogout">
                    {{ __('Log out Other Browser Sessions') }}
                </x-button>
            </x-slot>
        </x-modal>
    </x-slot>

</x-partials.action-section>
