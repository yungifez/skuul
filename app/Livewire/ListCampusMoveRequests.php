<?php

namespace App\Livewire;

use App\Actions\Enrollment\RequestCampusMove;
use App\Models\CampusMoveRequest;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

/**
 * The campus moves this campus must decide, and the ones it asked for.
 */
class ListCampusMoveRequests extends Component
{
    /**
     * The note the decider types before approving or rejecting.
     *
     * @var array<int, string>
     */
    public array $notes = [];

    /**
     * Agree, which moves the student in the same breath.
     */
    public function approve(int $requestId, RequestCampusMove $requestCampusMove): void
    {
        $this->decide($requestId, 'approve', $requestCampusMove);
    }

    /**
     * Say no, and leave the student where they are.
     */
    public function reject(int $requestId, RequestCampusMove $requestCampusMove): void
    {
        $this->decide($requestId, 'reject', $requestCampusMove);
    }

    /**
     * Take back a request this campus made.
     */
    public function cancel(int $requestId, RequestCampusMove $requestCampusMove): void
    {
        $request = CampusMoveRequest::query()->findOrFail($requestId);

        $this->authorize('cancel', $request);

        $requestCampusMove->cancel($request, auth()->user(), $this->noteFor($requestId));

        unset($this->notes[$requestId]);
        session()->flash('success', 'The campus move request was taken back.');
    }

    public function render(): View
    {
        $school = current_school();

        return view('livewire.list-campus-move-requests', [
            'incoming'   => $this->requestsFor('to_school_id', $school?->id),
            'outgoing'   => $this->requestsFor('from_school_id', $school?->id),
            'campusName' => $school?->name,
        ]);
    }

    /**
     * Approve or reject one request.
     */
    private function decide(int $requestId, string $decision, RequestCampusMove $requestCampusMove): void
    {
        $request = CampusMoveRequest::query()->findOrFail($requestId);

        $this->authorize('decide', $request);

        $requestCampusMove->{$decision}($request, auth()->user(), $this->noteFor($requestId));

        unset($this->notes[$requestId]);
        session()->flash(
            'success',
            $decision === 'approve'
                ? 'The student moved to this campus. Their enrollment and history came with them.'
                : 'The campus move was rejected. The student stays where they are.',
        );
    }

    /**
     * Get the open requests on one side of this campus.
     *
     * @return Collection<int, CampusMoveRequest>
     */
    private function requestsFor(string $column, ?int $schoolId): Collection
    {
        if ($schoolId === null) {
            return collect();
        }

        return CampusMoveRequest::query()
            ->where($column, $schoolId)
            ->open()
            ->with([
                'studentRecord.user',
                'fromSchool',
                'toSchool',
                'academicCycleSection.academicLevel',
                'requestedBy',
            ])
            ->latest('id')
            ->get();
    }

    /**
     * Get the note typed for one request, if there is one.
     */
    private function noteFor(int $requestId): ?string
    {
        $note = $this->notes[$requestId] ?? null;

        return filled($note) ? $note : null;
    }
}
