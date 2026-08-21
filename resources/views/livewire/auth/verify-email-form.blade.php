<div class="space-y-6">
    @if (session('status') === 'verification-link-sent')
        <april:alert variant="none">
            <slot:title>Verification link sent</slot:title>
            <slot:description>A new verification link has been sent to your email address.</slot:description>
        </april:alert>
    @endif

    <p class="text-sm leading-6 text-muted-foreground">
        Thanks for signing up. Verify your email address by clicking the link we sent you. If you did not receive it, request another link below.
    </p>

    <div class="grid gap-3" x-data="{ submitting: false }">
        <form method="POST" action="{{ route('verification.send') }}" x-on:submit="submitting = true">
            @csrf
            <april:button type="submit" class="w-full justify-center" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
                <span x-show="! submitting">Resend verification email</span>
                <span x-show="submitting" x-cloak>Working...</span>
            </april:button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <april:button type="submit" variant="ghost" class="w-full">Log out</april:button>
        </form>
    </div>
</div>
