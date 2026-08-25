<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class DisplayStatus extends Component
{
    public function render(): View
    {
        $notifications = [];

        foreach ([
            ['type' => 'danger', 'title' => 'Action failed', 'message' => session('danger')],
            ['type' => 'success', 'title' => 'Success', 'message' => session('success')],
            ['type' => 'info', 'title' => 'Info', 'message' => session('info')],
            ['type' => 'success', 'title' => 'Success', 'message' => session('status')],
        ] as $notification) {
            if (filled($notification['message'])) {
                $notifications[] = [
                    'id' => count($notifications),
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                    'message' => (string) $notification['message'],
                ];
            }
        }

        return view('livewire.display-status', compact('notifications'));
    }
}
