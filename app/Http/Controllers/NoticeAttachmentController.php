<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\Notice;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoticeAttachmentController extends Controller
{
    public function __construct(private PortalAccess $portalAccess)
    {
    }

    /** Download a private attachment when the person may read its notice. */
    public function __invoke(Request $request, Notice $notice): StreamedResponse
    {
        $person = $request->user();

        abort_unless($person instanceof User && $this->mayDownload($person, $notice), 403);
        abort_unless($notice->hasManagedAttachment() && Storage::disk('local')->exists($notice->attachment), 404);

        return Storage::disk('local')->download(
            $notice->attachment,
            $notice->attachment_name ?? 'notice-attachment',
            ['Content-Type' => $notice->attachment_mime_type ?? 'application/octet-stream'],
        );
    }

    /** Check both staff and family access without granting either more data. */
    private function mayDownload(User $person, Notice $notice): bool
    {
        if ($person->can('view', $notice)) {
            return true;
        }

        if (!$notice->isPublished() || !$this->portalAccess->areaIsOpen(PortalArea::Notices, $notice->school_id)) {
            return false;
        }

        $studentUserIds = $this->portalAccess->enrollmentsFor($person)
            ->where('school_id', $notice->school_id)
            ->pluck('user_id');

        return $studentUserIds->isNotEmpty()
            && $notice->recipients()->whereIn('user_id', $studentUserIds)->exists();
    }
}
