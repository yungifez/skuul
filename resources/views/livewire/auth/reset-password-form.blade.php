<div class="space-y-6">
    <form action="{{ route('password.update') }}" method="POST" class="grid gap-5" autocomplete="off" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-auth.field name="email" id="email" type="email" label="Email address" value="{{ $email }}" autocomplete="email" required />
        <x-auth.field name="password" id="password" type="password" label="New password" autocomplete="new-password" required />
        <x-auth.field name="password_confirmation" id="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password" required />

        <x-auth.submit label="Reset password" class="w-full" />
    </form>
</div>
