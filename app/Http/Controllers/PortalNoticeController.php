<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Notice\NoticeContentSanitizer;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalNoticeController extends Controller
{
    public function __construct(
        private PortalAccess $access,
        private PortalSummary $summary,
        private NoticeContentSanitizer $contentSanitizer,
    ) {}

    /** Show the notices delivered to one student account. */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Notices, $studentRecord->school_id), 404);

        $notices = $this->summary->notices($studentRecord);

        foreach ($notices as $recipient) {
            $recipient->notice->setAttribute(
                'safe_content',
                $this->contentSanitizer->sanitize((string) $recipient->notice->content),
            );
        }

        return view('pages.portal.notices', compact('notices', 'studentRecord'));
    }
}
