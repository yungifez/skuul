<div class="space-y-6">
    <form action="{{ route('invitations.accept', ['token' => $token]) }}" method="POST" class="grid gap-5" autocomplete="off" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <april:input-group name="email" id="email" type="email" label="Email address" value="{{ $email }}" autocomplete="username" disabled readonly />
        <april:input-group name="password" id="password" type="password" label="Choose a password" autocomplete="new-password" autofocus required />
        <april:input-group name="password_confirmation" id="password_confirmation" type="password" label="Confirm password" autocomplete="new-password" required />

        <april:button type="submit" class="w-full justify-center" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
            <span x-show="! submitting">Set password and sign in</span>
            <span x-show="submitting" x-cloak>Working...</span>
        </april:button>
    </form>
</div>
