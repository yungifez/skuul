<div x-data="{ recovery: false, submitting: false }" class="space-y-6">
    <div class="space-y-2 text-sm leading-6 text-muted-foreground">
        <p x-show="! recovery">Enter the authentication code from your authenticator application.</p>
        <p x-show="recovery" x-cloak>Enter one of your emergency recovery codes.</p>
    </div>

    <form action="{{ route('two-factor.login') }}" method="POST" class="grid gap-5" x-on:submit="submitting = true">
        @csrf

        <div x-show="! recovery">
            <x-auth.field name="code" id="code" label="Authentication code" inputmode="numeric" autocomplete="one-time-code" autofocus x-ref="authenticationCode" />
        </div>

        <div x-show="recovery" x-cloak>
            <x-auth.field name="recovery_code" id="recovery-code" label="Recovery code" autocomplete="one-time-code" x-ref="recoveryCode" />
        </div>

        <div class="flex flex-col gap-3">
            <x-auth.submit label="Log in" class="w-full" />

            <april:button
                type="button"
                variant="ghost"
                class="w-full"
                x-show="! recovery"
                x-on:click="recovery = true; $nextTick(() => $refs.recoveryCode.focus())"
            >
                Use a recovery code
            </april:button>

            <april:button
                type="button"
                variant="ghost"
                class="w-full"
                x-show="recovery"
                x-cloak
                x-on:click="recovery = false; $nextTick(() => $refs.authenticationCode.focus())"
            >
                Use an authentication code
            </april:button>
        </div>
    </form>
</div>
