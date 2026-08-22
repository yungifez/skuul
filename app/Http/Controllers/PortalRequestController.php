<?php

namespace App\Http\Controllers;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StorePortalRequestRequest;
use App\Http\Requests\UpdatePortalRequestRequest;
use App\Models\PortalRequest;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * What a family asked the school for, and what the school answered.
 *
 * A request changes nothing by itself. It is a message with a state, so the
 * portal stays read-only over the school's records.
 */
class PortalRequestController extends Controller
{
    public function __construct(
        private SubmitPortalRequest $submitRequest,
        private PortalAccess $access,
    ) {}

    /**
     * Show one family the requests they sent about one learner.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Requests, $studentRecord->school_id), 404);

        return view('pages.portal.requests', [
            'studentRecord' => $studentRecord->load('user:id,name'),
            'requests' => PortalRequest::query()
                ->where('student_record_id', $studentRecord->id)
                ->where('requested_by', $request->user()->id)
                ->latest('id')
                ->get(),
            'types' => PortalRequestType::cases(),
        ]);
    }

    /**
     * Send a request about one learner.
     */
    public function store(StorePortalRequestRequest $request, StudentRecord $studentRecord): RedirectResponse
    {
        try {
            $this->submitRequest->submit(
                enrollment: $studentRecord,
                subject: $request->string('subject')->toString(),
                type: PortalRequestType::from($request->string('type')->toString()),
                message: $request->string('message')->toString() ?: null,
                person: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['portal_request' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'Your request was sent to the school.');
    }

    /**
     * Show the school what families have asked for.
     */
    public function inbox(Request $request): View
    {
        abort_unless($request->user()?->can('read portal request'), 403);

        $selectedStatus = PortalRequestStatus::tryFrom($request->string('status')->toString());
        $selectedType = PortalRequestType::tryFrom($request->string('type')->toString());

        $requests = PortalRequest::query()
            ->inSchool()
            ->with(['studentRecord.user:id,name', 'requestedBy:id,name', 'answeredBy:id,name'])
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.portal-request.index', [
            'requests' => $requests,
            'statuses' => PortalRequestStatus::cases(),
            'types' => PortalRequestType::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedType' => $selectedType,
            'waitingCount' => PortalRequest::query()
                ->inSchool()
                ->whereIn('status', [PortalRequestStatus::Submitted, PortalRequestStatus::InReview])
                ->count(),
        ]);
    }

    /**
     * Answer a request, or move it on.
     */
    public function changeStatus(UpdatePortalRequestRequest $request, PortalRequest $portalRequest): RedirectResponse
    {
        try {
            $this->submitRequest->changeStatus(
                request: $portalRequest,
                status: PortalRequestStatus::from($request->string('status')->toString()),
                actor: $request->user(),
                response: $request->string('response')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('success', 'The family will see the answer in the portal.');
    }
}
