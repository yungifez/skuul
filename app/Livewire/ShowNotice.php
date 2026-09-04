<?php

namespace App\Livewire;

use App\Actions\Notice\PublishNotice;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Notice;
use App\Services\Notice\NoticeContentSanitizer;
use Illuminate\View\View;
use Livewire\Component;

class ShowNotice extends Component
{
    use DispatchesStatusNotifications;

    public Notice $notice;

    public function publishNotice(PublishNotice $publishNotice): void
    {
        $this->authorize('update', $this->notice);

        try {
            $this->notice = $publishNotice->publish($this->notice, auth()->user());
            $this->notify('Notice published successfully');
        } catch (InvalidValueException $exception) {
            $this->addError('notice', $exception->getMessage());
        }
    }

    public function render(NoticeContentSanitizer $contentSanitizer): View
    {
        return view('livewire.show-notice', [
            'content' => $contentSanitizer->sanitize((string) $this->notice->content),
        ]);
    }
}
