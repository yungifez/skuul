<div class="space-y-6">
    <p class="text-sm leading-6 text-muted-foreground">
        This is a secure area of the application. Please confirm your password before continuing.
    </p>

    <form action="{{ route('password.confirm') }}" method="POST" class="grid gap-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        <x-auth.field name="password" id="password" type="password" label="Current password" autocomplete="current-password" autofocus required />
        <x-auth.submit label="Confirm password" class="w-full" />
    </form>
</div>
