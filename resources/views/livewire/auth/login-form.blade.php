<div class="space-y-6">
    <form action="{{ route('login') }}" method="POST" class="grid gap-5" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <x-auth.field name="email" id="email" type="email" label="Email address" autocomplete="email" autofocus required />
        <x-auth.field name="password" id="password" type="password" label="Password" autocomplete="current-password" required />

        <label for="remember" class="flex items-center gap-3 text-sm text-muted-foreground">
            <april:input type="checkbox" id="remember" name="remember" value="1" :checked="old('remember')" />
            <span>Remember me</span>
        </label>

        <x-auth.submit label="Log in" class="w-full" />
    </form>

    <p class="text-center text-sm text-muted-foreground">
        Your school's administrator creates your account and emails you an invitation link.
    </p>
</div>
