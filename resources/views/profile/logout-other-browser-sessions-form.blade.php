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
                                <x-lucide-monitor class="size-8 text-muted" />
                            @else
                                <x-lucide-smartphone class="size-8 text-muted" />
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

        <april:dialog dismissable x-effect="show = $wire.confirmingLogout">
            <slot:content class="sm:max-w-md">
                <april:dialog-header>
                    <slot:title>Log Out Other Browser Sessions</slot:title>
                    <slot:description>{{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}</slot:description>
                </april:dialog-header>

                <div class="my-3" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <april:input-group id="password-for-logout" name="password" type="password" placeholder="{{ __('Password') }}" label="Confirm Password to continue" x-ref="password" class="w-full" wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                </div>

                <april:dialog-footer>
                    <april:button variant="destructive" class="text-sm px-2 md:px-4" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                        {{ __('Log out Other Browser Sessions') }}
                    </april:button>
                </april:dialog-footer>
            </slot:content>
        </april:dialog>
    </x-slot>

</x-partials.action-section>
