<?php

namespace App\Services\Notice;

use App\Models\Notice;

class NoticeService
{
    /**
     * Get all notices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllNotices()
    {
        return Notice::where('school_id', auth()->user()->school_id)->get();
    }

    /**
     * Get present notices which are active.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPresentNotices()
    {
        return Notice::where('school_id', auth()->user()->school_id)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('stop_date', '>=', date('Y-m-d'))
            ->where('active', 1)
            ->get();
    }

    /**
     * Store notice.
     *
     * @return \App\Models\Notice
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
            'school_id'  => auth()->user()->school_id,
        ]);

        return $notice;
    }

    /**
     * Delete notice.
     *
     * @param \App\Models\Notice $notice
     *
     * @return void
     */
    public function deleteNotice(Notice $notice)
    {
        $notice->delete();
    }
}
