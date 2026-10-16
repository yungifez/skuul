@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
@endphp
<form wire:submit="save" autocomplete="off" class="max-w-3xl space-y-6">
    @include('livewire.partials.person-fields', ['photoUrl' => $student->profile_photo_url])
    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save,profilePhoto">Save changes</april:button>
</form>
