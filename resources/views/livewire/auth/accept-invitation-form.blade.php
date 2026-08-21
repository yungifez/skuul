<div class="space-y-6">
    <form action="{{ route('invitations.accept', ['token' => $token]) }}" method="POST" class="grid gap-5" autocomplete="off" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <x-auth.field name="email" id="email" type="email" label="Email address" value="{{ $email }}" autocomplete="username" disabled readonly />
        <x-auth.field name="password" id="password" type="password" label="Choose a password" autocomplete="new-password" autofocus required />
        <x-auth.field name="password_confirmation" id="password_confirmation" type="password" label="Confirm password" autocomplete="new-password" required />

        <x-auth.submit label="Set password and sign in" class="w-full" />
    </form>
</div>
