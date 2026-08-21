<div class="space-y-6">
    <form action="{{ route('login') }}" method="POST" class="grid gap-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <april:input-group name="email" id="email" type="email" label="Email address" autocomplete="email" autofocus required />
        <april:input-group name="password" id="password" type="password" label="Password" autocomplete="current-password" required />

        <label for="remember" class="flex items-center gap-3 text-sm text-muted-foreground">
            <april:input type="checkbox" id="remember" name="remember" value="1" :checked="old('remember')" />
            <span>Remember me</span>
        </label>

        <april:button type="submit" class="w-full justify-center" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
            <span x-show="! submitting">Log in</span>
            <span x-show="submitting" x-cloak>Working...</span>
        </april:button>
    </form>

    <p class="text-center text-sm text-muted-foreground">
        Your school's administrator creates your account and emails you an invitation link.
    </p>
</div>
