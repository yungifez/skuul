<div class="space-y-6">
    <form action="{{ route('password.update') }}" method="POST" class="grid gap-5" autocomplete="off" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <april:input-group name="email" id="email" type="email" label="Email address" value="{{ $email }}" autocomplete="email" required />
        <april:input-group name="password" id="password" type="password" label="New password" autocomplete="new-password" required />
        <april:input-group name="password_confirmation" id="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password" required />

        <april:button type="submit" class="w-full justify-center" x-bind:disabled="submitting" x-bind:aria-busy="submitting">
            <span x-show="! submitting">Reset password</span>
            <span x-show="submitting" x-cloak>Working...</span>
        </april:button>
    </form>
</div>
