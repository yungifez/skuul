@extends('layouts.guest')

@section('title', 'Two Factor Challenge')

@section('body')
    <x-partials.authentication-card>
        <div class="p-4">
            <x-display-validation-errors />
            <div x-data="{ recovery: false }">
                <div class="mb-3 text-sm md:text-base" x-show="! recovery">
                    {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </div>

                <div class="mb-3 text-sm md:text-base" x-show="recovery">
                    {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                </div>

                <form method="POST" action="{{ route('two-factor.login') }}">
                    @csrf

                    <div class="form-group" x-show="! recovery">
                        <x-input id="code" label="Code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                    </div>

                    <div class="form-group" x-show="recovery">
                        <x-input label="Recovery Code" id="recovery-code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                    </div>

                    <div class="flex justify-end mt-3">
                        <x-button type="button" class="bg-gray-800"
                                x-show="! recovery"
                                x-on:click="
                                            recovery = true;
                                            $nextTick(() => { $refs.recovery_code.focus() })
                                        ">
                            {{ __('Use a recovery code') }}
                        </x-button>

                        <x-button type="button" class="bg-gray-800 text-xs md:text-base px-2"
                                x-show="recovery"
                                x-on:click="
                                            recovery = false;
                                            $nextTick(() => { $refs.code.focus() })
                                        ">
                            {{ __('Use an authentication code') }}
                        </x-button>

                        <x-button class="px-2 md:px-6">
                            {{ __('Log in') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </x-partials.authentication-card>
@endsection
           
