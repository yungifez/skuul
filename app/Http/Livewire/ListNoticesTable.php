<?php

namespace App\Http\Livewire;

use App\Services\Notice\NoticeService;
use Livewire\Component;

class ListNoticesTable extends Component
{
    public $notices;

    public function mount(NoticeService $noticeService)
    {
        //if user doesnt have update permission, only display present and active notices since he is
        //not managing notices
        if (auth()->user()->can('update notice')) {
            $this->notices = $noticeService->getAllNotices();
        } else {
            $this->notices = $noticeService->getPresentNotices();
        }
    }

    public function render()
    {
        return view('livewire.list-notices-table');
    }
}
