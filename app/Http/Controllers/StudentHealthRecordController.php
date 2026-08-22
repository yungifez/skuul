<?php

namespace App\Http\Controllers;

use App\Actions\Wellbeing\RecordHealthInformation;
use App\Http\Requests\UpdateStudentHealthRecordRequest;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Keep the health facts the school needs in an emergency.
 *
 * One child has one record. Reading a student profile does not open this, so
 * the screens live apart from the student screens.
 */
class StudentHealthRecordController extends Controller
{
    public function __construct(private RecordHealthInformation $recordHealthInformation) {}

    /**
     * Show every learner, and whether the school holds their health facts.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StudentHealthRecord::class);

        $search = $request->string('search')->toString() ?: null;
        $missingOnly = $request->boolean('missing');

        $learners = StudentRecord::query()
            ->inSchool()
            ->with(['user:id,name', 'healthRecord'])
            ->when($search !== null, function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('admission_number', 'like', "%$search%")
                        ->orWhereHas('user', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($missingOnly, function (Builder $query): void {
                $query->whereDoesntHave('healthRecord');
            })
            ->orderBy('admission_number')
            ->paginate(20)
            ->withQueryString();

        return view('pages.health-record.index', [
            'learners' => $learners,
            'search' => $search,
            'missingOnly' => $missingOnly,
            'recordedCount' => StudentRecord::query()->inSchool()->whereHas('healthRecord')->count(),
            'learnerCount' => StudentRecord::query()->inSchool()->count(),
        ]);
    }

    /**
     * Show the health record of one learner.
     */
    public function edit(StudentRecord $studentRecord): View
    {
        $this->authorize('viewAny', StudentHealthRecord::class);

        abort_unless($studentRecord->school_id === current_school_id(), 404);

        $studentRecord->load(['user:id,name', 'healthRecord.updatedBy:id,name']);

        return view('pages.health-record.edit', [
            'enrollment' => $studentRecord,
            'record' => $studentRecord->healthRecord,
        ]);
    }

    /**
     * Write the health record of one learner.
     */
    public function update(UpdateStudentHealthRecordRequest $request, StudentRecord $studentRecord): RedirectResponse
    {
        abort_unless($studentRecord->school_id === current_school_id(), 404);

        $this->recordHealthInformation->record($studentRecord, $request->validated(), $request->user());

        return redirect()
            ->route('health-records.edit', $studentRecord)
            ->with('success', 'The health record was saved.');
    }
}
