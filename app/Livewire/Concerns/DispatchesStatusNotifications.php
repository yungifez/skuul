<?php

namespace App\Livewire\Concerns;

use Livewire\Component;

/**
 * Keep Livewire actions visible in the shared status display.
 *
 * @mixin Component
 */
trait DispatchesStatusNotifications
{
    protected function notify(string $message, string $type = 'success'): void
    {
        session()->flash($type, $message);
        $this->dispatch('status-message', type: $type, message: $message);
    }
}
