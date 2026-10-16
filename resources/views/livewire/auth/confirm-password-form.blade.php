<div class="space-y-6">
    <p class="text-sm leading-6 text-muted-foreground">
        This is a secure area of the application. Please confirm your password before continuing.
    </p>

    <form action="{{ route('password.confirm') }}" method="POST" class="grid gap-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        <april:input-group name="password" id="password" type="password" label="Current password" autocomplete="current-password" autofocus required />
        <april:button type="submit" class="w-full justify-center" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
            <span x-show="! submitting">Confirm password</span>
            <span x-show="submitting" x-cloak>Working...</span>
        </april:button>
    </form>
</div>
