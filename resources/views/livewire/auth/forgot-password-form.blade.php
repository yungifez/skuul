<div class="space-y-6">
    <form action="{{ route('password.email') }}" method="POST" class="grid gap-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <x-auth.field name="email" id="email" type="email" label="Email address" autocomplete="email" autofocus required />
        <x-auth.submit label="Email reset link" class="w-full" />
    </form>

    <p class="text-center text-sm text-muted-foreground">
        Remember your password?
        <a href="{{ route('login') }}" class="font-medium text-foreground underline-offset-4 hover:underline" aria-label="Login">
            Back to login
        </a>
    </p>
</div>
