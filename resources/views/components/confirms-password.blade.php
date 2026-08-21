@props(['title' => __('Confirm Password'), 'content' => __('For your security, please confirm your password to continue.'), 'button' => __('Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp
<x-loading-spinner wire:target="confirmPassword"/>

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
<april:dialog dismissable x-effect="show = $wire.confirmingPassword">
    <slot:content class="sm:max-w-md">
        <april:dialog-header>
            <slot:title>{{ $title }}</slot:title>
            <slot:description>{{ $content }}</slot:description>
        </april:dialog-header>

        <div class="grid gap-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <april:input-group id="confirmable-password" type="password" name="confirmable_password" placeholder="{{ __('Password') }}" label="{{ __('Password') }}" wire:model="confirmablePassword" x-ref="confirmable_password" x-on:keydown.enter="confirmPassword" />
        </div>

        <april:dialog-footer>
            <april:button class="ms-2" wire:click="confirmPassword" wire:loading.attr="disabled">
                {{ $button }}
            </april:button>
        </april:dialog-footer>
    </slot:content>
</april:dialog>
@endonce
