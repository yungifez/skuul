<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * What a family asked the school for, and what the school answered.
 *
 * A request changes nothing by itself. It is a message with a state, so the
 * portal stays read-only over the school's records. Sending, taking back and
 * answering happen in the Livewire screens.
 */
class PortalRequestController extends Controller
{
    public function __construct(private PortalAccess $access) {}

    /**
     * Show one family the requests they sent about one learner.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Requests, $studentRecord->school_id), 404);

        return view('pages.portal.requests', ['studentRecord' => $studentRecord]);
    }

    /**
     * Show the school the requests families sent.
     */
    public function inbox(Request $request): View
    {
        abort_unless($request->user()?->can('read portal request'), 403);

        return view('pages.portal-request.index');
    }
}
