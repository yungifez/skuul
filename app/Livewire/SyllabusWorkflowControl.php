<?php

namespace App\Livewire;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReturnSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Actions\Syllabus\SubmitSyllabus;
use App\Enums\SyllabusStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Move a syllabus through review: submit, withdraw, approve, send back, and revise.
 */
class SyllabusWorkflowControl extends Component
{
    use DispatchesStatusNotifications;

    public Syllabus $syllabus;

    public string $reviewNote = '';

    public string $changeNote = '';

    public function submit(SubmitSyllabus $submitSyllabus): void
    {
        Gate::authorize('submit', $this->syllabus);

        $this->run(fn () => $submitSyllabus->submit($this->syllabus, auth()->user()), 'Sent for review.', 'syllabi.show');
    }

    public function withdraw(SubmitSyllabus $submitSyllabus): void
    {
        Gate::authorize('withdraw', $this->syllabus);

        $this->run(fn () => $submitSyllabus->withdraw($this->syllabus), 'Taken back from review. You can edit the draft again.', 'syllabi.edit');
    }

    public function publish(PublishSyllabus $publishSyllabus): void
    {
        Gate::authorize('publish', $this->syllabus);

        $this->run(fn () => $publishSyllabus->publish($this->syllabus, auth()->user()), 'Syllabus approved and published.', 'syllabi.show');
    }

    public function sendBack(ReturnSyllabus $returnSyllabus): void
    {
        Gate::authorize('sendBack', $this->syllabus);
        $this->validate(['reviewNote' => ['required', 'string', 'max:2000']], ['reviewNote.required' => 'Say what needs to change.']);

        $this->run(fn () => $returnSyllabus->sendBack($this->syllabus, $this->reviewNote, auth()->user()), 'Sent back to the author with your note.', 'syllabi.show');
    }

    public function revise(ReviseSyllabus $reviseSyllabus): void
    {
        Gate::authorize('revise', $this->syllabus);
        $this->validate(['changeNote' => ['required', 'string', 'max:2000']], ['changeNote.required' => 'Say what will change in this revision and why.']);

        try {
            $revision = $reviseSyllabus->revise($this->syllabus, ['change_note' => $this->changeNote], auth()->user());
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', 'A revised draft was created. Make the changes, then send it for review.');
        $this->redirectRoute('syllabi.edit', $revision);
    }

    public function render(): View
    {
        return view('livewire.syllabus-workflow-control', [
            'canSubmit' => Gate::allows('submit', $this->syllabus),
            'canWithdraw' => Gate::allows('withdraw', $this->syllabus),
            'canPublish' => Gate::allows('publish', $this->syllabus),
            'canSendBack' => Gate::allows('sendBack', $this->syllabus),
            'canRevise' => Gate::allows('revise', $this->syllabus) && $this->syllabus->openRevision() === null,
            'isDraft' => $this->syllabus->status === SyllabusStatus::Draft,
        ]);
    }

    /**
     * Run a transition, then reload the page it leaves the syllabus on.
     */
    private function run(callable $transition, string $message, string $route): void
    {
        try {
            $transition();
        } catch (ApplicationException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', $message);
        $this->redirectRoute($route, $this->syllabus);
    }
}
