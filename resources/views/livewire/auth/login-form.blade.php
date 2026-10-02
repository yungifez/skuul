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

    @if (config('demo.enabled'))
        <section class="space-y-3" aria-labelledby="demo-accounts-heading" x-data>
            <div class="space-y-1">
                <h2 id="demo-accounts-heading" class="text-sm font-medium">Try the demo as</h2>
                <p class="text-sm text-muted-foreground">Every account uses the password <span class="font-mono">{{ config('demo.password') }}</span>. The demo resets every hour, and deleting is turned off.</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach (config('demo.accounts') as $role => $email)
                    <april:button type="button" variant="outline" class="min-h-11 justify-center" x-on:click="document.getElementById('email').value = '{{ $email }}'; document.getElementById('password').value = '{{ config('demo.password') }}'; document.getElementById('password').form.requestSubmit()">{{ $role }}</april:button>
                @endforeach
            </div>
        </section>
    @else
        <p class="text-center text-sm text-muted-foreground">
            Your school's administrator creates your account and emails you an invitation link.
        </p>
    @endif
</div>
