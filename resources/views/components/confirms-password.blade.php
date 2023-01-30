@props(['title' => __('Confirm Password'), 'content' => __('For your security, please confirm your password to continue.'), 'button' => __('Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-modal x-effect="modal = $wire.confirmingPassword" button="">
    <x-slot name="title">
        {{ $title }}
    </x-slot>

        <p class="p-3 text-center">
            {{ $content }}
        </p>

        <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-input label="Confirm Password" id="password" type="password" name="confirmable_password" placeholder="{{ __('Password') }}"
            wire:model.defer="confirmablePassword"
            x-ref="confirmable_password"
            x-on:keydown.enter="confirmPassword" />
        </div>

    <x-slot name="footer">
        <x-button class="ms-2" wire:click="confirmPassword" wire:loading.attr="disabled">
            {{ $button }}
        </x-button>
    </x-slot>
</x--modal>
@endonce
