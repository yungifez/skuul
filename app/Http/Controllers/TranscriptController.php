<?php

namespace App\Http\Controllers;

use App\Actions\Report\PublishTranscript;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\StoreTranscriptRequest;
use App\Models\StudentRecord;
use App\Models\TranscriptSnapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TranscriptController extends Controller
{
    public function __construct(private PublishTranscript $publishTranscript) {}

    public function index(): View
    {
        $this->authorize('viewAny', TranscriptSnapshot::class);

        return view('pages.transcript.index');
    }

    public function store(StoreTranscriptRequest $request): RedirectResponse
    {
        $student = StudentRecord::query()->inSchool()->findOrFail($request->integer('student_record_id'));

        try {
            $this->publishTranscript->publish($student, $request->user(), $request->string('reason')->toString() ?: null);
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['transcript' => $exception->getMessage()]);
        }

        return back()->with('success', 'Transcript issued.');
    }
}
