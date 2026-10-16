@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
@endphp
<form wire:submit="save" autocomplete="off" class="max-w-3xl space-y-6">
    @include('livewire.partials.person-fields', ['photoUrl' => asset('application-images/user-profile-image.png')])
    <p class="text-sm text-muted-foreground">They get an email with a link to set their own password. Someone who already has an account here signs in as before.</p>
    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save,profilePhoto">Add parent</april:button>
</form>
