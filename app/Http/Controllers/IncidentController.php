<?php

namespace App\Http\Controllers;

use App\Actions\Discipline\ReportIncident;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreIncidentActionRequest;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentStatusRequest;
use App\Models\Incident;
use App\Models\IncidentAction;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Record a case, follow it, and close it.
 *
 * A safeguarding case is readable only by the people who handle it, so every
 * list here goes through the case's own readable-by rule as well as the policy.
 */
class IncidentController extends Controller
{
    use ListsSchoolPeople;

    public function __construct(private ReportIncident $reportIncident) {}

    /**
     * Show the cases this person may read.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Incident::class);

        $selectedStatus = IncidentStatus::tryFrom($request->string('status')->toString());
        $selectedCategory = IncidentCategory::tryFrom($request->string('category')->toString());
        $openOnly = $request->boolean('open');

        $incidents = Incident::query()
            ->inSchool()
            ->readableBy($request->user())
            ->with(['assignedTo:id,name', 'reportedBy:id,name'])
            ->withCount('participants')
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->when($selectedCategory !== null, function (Builder $query) use ($selectedCategory): void {
                $query->where('category', $selectedCategory);
            })
            ->when($openOnly, function (Builder $query): void {
                $query->open();
            })
            ->latest('occurred_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.incident.index', [
            'incidents' => $incidents,
            'statuses' => IncidentStatus::cases(),
            'categories' => IncidentCategory::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory,
            'openOnly' => $openOnly,
            'openCount' => Incident::query()->inSchool()->readableBy($request->user())->open()->count(),
        ]);
    }

    /**
     * Show the form that records a case.
     */
    public function create(): View
    {
        $this->authorize('create', Incident::class);

        return view('pages.incident.create', [
            'categories' => IncidentCategory::cases(),
            'roles' => IncidentParticipantRole::cases(),
            'students' => $this->schoolLearners(),
            'staff' => $this->schoolStaff(),
        ]);
    }

    /**
     * Record a case.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $assignee = $request->filled('assigned_to')
            ? User::findOrFail($request->integer('assigned_to'))
            : null;

        try {
            $incident = $this->reportIncident->report(
                summary: $request->string('summary')->toString(),
                category: IncidentCategory::from($request->string('category')->toString()),
                description: $request->string('description')->toString() ?: null,
                occurredAt: $request->string('occurred_at')->toString(),
                participants: $this->participantsFrom($request),
                reporter: $request->user(),
                assignee: $assignee,
                location: $request->string('location')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['incident' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('incidents.show', $incident)->with('success', "Case $incident->reference was recorded.");
    }

    /**
     * Show one case with everything recorded against it.
     */
    public function show(Incident $incident): View
    {
        $this->authorize('view', $incident);

        $incident->load([
            'participants.user:id,name',
            'participants.studentRecord.user:id,name',
            'actions.assignedTo:id,name',
            'statusChanges.changedBy:id,name',
            'reportedBy:id,name',
            'assignedTo:id,name',
        ]);

        return view('pages.incident.show', [
            'incident' => $incident,
            'nextStatuses' => $incident->status->allowedNext(),
            'staff' => $this->schoolStaff(),
        ]);
    }

    /**
     * Move the case to another state.
     */
    public function changeStatus(UpdateIncidentStatusRequest $request, Incident $incident): RedirectResponse
    {
        try {
            $this->reportIncident->changeStatus(
                incident: $incident,
                status: IncidentStatus::from($request->string('status')->toString()),
                actor: $request->user(),
                reason: $request->string('reason')->toString() ?: null,
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('success', 'The case moved to '.$incident->fresh()->status->label().'.');
    }

    /**
     * Record something the school will do about the case.
     */
    public function storeAction(StoreIncidentActionRequest $request, Incident $incident): RedirectResponse
    {
        $assignee = $request->filled('assigned_to')
            ? User::findOrFail($request->integer('assigned_to'))
            : null;

        try {
            $this->reportIncident->addAction(
                incident: $incident,
                type: $request->string('type')->toString(),
                description: $request->string('description')->toString(),
                dueOn: $request->string('due_on')->toString() ?: null,
                assignee: $assignee,
                actor: $request->user(),
            );
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['action' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'The action was added to the case.');
    }

    /**
     * Record that an action is done.
     */
    public function completeAction(Incident $incident, IncidentAction $incidentAction): RedirectResponse
    {
        $this->authorize('update', $incident);

        abort_unless($incidentAction->incident_id === $incident->id, 404);

        $incidentAction->complete();

        return back()->with('success', 'The action was marked done.');
    }

    /**
     * Read the participants a form sent, dropping the empty rows.
     *
     * The form always renders a few blank rows, so a row that names nobody is
     * not an error. It simply was not filled in.
     *
     * @return array<int, array{enrollment: int, role: IncidentParticipantRole, note: string|null}>
     */
    private function participantsFrom(Request $request): array
    {
        $participants = [];

        /** @var array<int, array<string, mixed>> $rows */
        $rows = $request->input('participants', []);

        foreach ($rows as $row) {
            $enrollment = $row['student_record_id'] ?? null;

            if (blank($enrollment)) {
                continue;
            }

            $participants[] = [
                'enrollment' => (int) $enrollment,
                'role' => IncidentParticipantRole::tryFrom((string) ($row['role'] ?? '')) ?? IncidentParticipantRole::Subject,
                'note' => blank($row['note'] ?? null) ? null : (string) $row['note'],
            ];
        }

        return $participants;
    }
}
