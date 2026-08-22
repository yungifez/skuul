<?php

namespace App\Http\Controllers;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Gradebook\SaveGradingScale;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreGradingScaleRequest;
use App\Http\Requests\UpdateGradingScaleRequest;
use App\Models\GradingScale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradingScaleController extends Controller
{
    public function __construct(
        private SaveGradingScale $saveGradingScale,
        private RecordAuditEvent $audit,
    ) {
    }

    /**
     * Display the school’s reusable grading scales and creation form.
     */
    public function index(): View
    {
        $this->authorize('viewAny', GradingScale::class);

        return view('pages.grading-scale.index', [
            'gradingScales' => GradingScale::query()
                ->inSchool()
                ->with('options')
                ->withCount('gradeItems')
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /** Create one grading scale. */
    public function store(StoreGradingScaleRequest $request): RedirectResponse
    {
        $this->saveGradingScale->create($request->validated(), $request->user());

        return to_route('grading-scales.index')->with('success', 'Grading scale created.');
    }

    /** Update one grading scale. */
    public function update(UpdateGradingScaleRequest $request, GradingScale $gradingScale): RedirectResponse
    {
        try {
            $this->saveGradingScale->update($gradingScale, $request->validated(), $request->user());
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['grading_scale' => $exception->getMessage()]);
        }

        return to_route('grading-scales.index')->with('success', 'Grading scale updated.');
    }

    /** Delete a scale that no assessment item uses. */
    public function destroy(GradingScale $gradingScale): RedirectResponse
    {
        $this->authorize('delete', $gradingScale);

        if ($gradingScale->gradeItems()->exists()) {
            return back()->withErrors(['grading_scale' => 'This scale is used by an assessment. Make it inactive instead of deleting it.']);
        }

        $gradingScale->delete();
        $this->audit->record(AuditAction::GradingScaleDeleted, $gradingScale, [], request()->user());

        return to_route('grading-scales.index')->with('success', 'Grading scale deleted.');
    }
}
