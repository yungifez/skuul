<?php

namespace App\Services\Notice;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NoticeService
{
    public function __construct(private NoticeContentSanitizer $contentSanitizer) {}

    /**
     * Get all notices.
     */
    public function getAllNotices(): Collection
    {
        return Notice::inSchool()->get();
    }

    /**
     * Get present notices which are active.
     */
    public function getPresentNotices(): Collection
    {
        return Notice::inSchool()
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('stop_date', '>=', date('Y-m-d'))
            ->where('active', 1)
            ->get();
    }

    /**
     * Store notice.
     */
    /**
     * Store the attachment on the private disk and create the draft notice.
     *
     * @param  array{title: string, content: string, start_date: string, stop_date: string, attachment?: UploadedFile|null, audience?: array<string, mixed>|null}  $data
     */
    public function storeNotice(array $data): Notice
    {
        /** @var UploadedFile|null $attachment */
        $attachment = $data['attachment'] ?? null;
        $schoolId = current_school_id();
        $attachmentPath = null;

        if ($attachment instanceof UploadedFile) {
            $attachmentPath = $attachment->store("notice-attachments/$schoolId", 'local');
        }

        try {
            return Notice::create([
                'title' => $data['title'],
                'content' => $this->contentSanitizer->sanitize($data['content']),
                'start_date' => $data['start_date'],
                'stop_date' => $data['stop_date'],
                'attachment' => $attachmentPath,
                'attachment_disk' => $attachmentPath === null ? null : 'local',
                'attachment_name' => $attachment?->getClientOriginalName(),
                'attachment_mime_type' => $attachment?->getMimeType(),
                'attachment_size' => $attachment?->getSize(),
                'audience' => $data['audience'] ?? null,
                'school_id' => $schoolId,
            ]);
        } catch (\Throwable $exception) {
            if ($attachmentPath !== null) {
                Storage::disk('local')->delete($attachmentPath);
            }

            throw $exception;
        }
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
