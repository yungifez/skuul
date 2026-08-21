<?php

namespace App\Services\Notice;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Collection;

class NoticeService
{
    /**
     * Get all notices.
     *
     * @return Collection
     */
    public function getAllNotices()
    {
        return Notice::inSchool()->get();
    }

    /**
     * Get present notices which are active.
     *
     * @return Collection
     */
    public function getPresentNotices()
    {
        return Notice::inSchool()
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('stop_date', '>=', date('Y-m-d'))
            ->where('active', 1)
            ->get();
    }

    /**
     * Store notice.
     *
     * @return Notice
     */
    public function storeNotice(array $data)
    {
        if (isset($data['attachment'])) {
            $data['attachment'] = $data['attachment']->store('notice/', 'public');
        } else {
            $data['attachment'] = null;
        }
        $notice = Notice::create([
            'title'      => $data['title'],
            'content'    => $data['content'],
            'start_date' => $data['start_date'],
            'stop_date'  => $data['stop_date'],
            'attachment' => $data['attachment'],
            'school_id'  => current_school_id(),
        ]);

        return $notice;
    }

    /**
     * Delete notice.
     *
     *
     * @return void
     */
    public function deleteNotice(Notice $notice)
    {
        $notice->delete();
    }
}
