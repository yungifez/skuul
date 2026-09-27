<?php

namespace App\Http\Controllers;

use App\Actions\Discipline\ReportIncident;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreIncidentRequest;
use App\Models\Incident;
use App\Models\User;
use App\Traits\ListsSchoolPeople;
use Illuminate\Contracts\View\View;
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

    public function __construct(
        private ReportIncident $reportIncident,
    ) {}

    /**
     * Show the cases this person may read.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        return view('pages.incident.index');
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

        return view('pages.incident.show', ['incident' => $incident]);
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
