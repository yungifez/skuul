<?php

namespace App\Livewire;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\PortalArea;
use App\Enums\PortalRequestType;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\PortalRequest;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Let a family ask the school about one learner and read the answers.
 *
 * Only the person who asked sees their own requests. Access is checked again
 * on every send, so a guardian whose link ended cannot keep asking.
 */
class PortalRequests extends Component
{
    use DispatchesStatusNotifications;

    public StudentRecord $studentRecord;

    public string $subject = '';

    public string $type = '';

    public string $message = '';

    public function mount(StudentRecord $studentRecord, PortalAccess $access): void
    {
        $this->ensureAccess($access);

        $this->subject = request()->string('subject')->limit(255, '')->toString();
        $this->type = (PortalRequestType::tryFrom(request()->string('type')->toString()) ?? PortalRequestType::Document)->value;
        $this->message = request()->string('message')->limit(2000, '')->toString();
    }

    public function send(SubmitPortalRequest $submitRequest, PortalAccess $access): void
    {
        $this->ensureAccess($access);

        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PortalRequestType::class)],
            'message' => ['nullable', 'string', 'max:2000'],
        ], ['subject.required' => 'Say what you need.']);

        try {
            $submitRequest->submit(
                enrollment: $this->studentRecord,
                subject: trim($this->subject),
                type: PortalRequestType::from($this->type),
                message: trim($this->message) === '' ? null : trim($this->message),
                person: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('subject', $exception->getMessage());

            return;
        }

        $this->reset('subject', 'message');
        $this->notify('Your request was sent to the school.');
    }

    public function withdraw(int $requestId, SubmitPortalRequest $submitRequest, PortalAccess $access): void
    {
        $this->ensureAccess($access);

        $request = PortalRequest::query()
            ->where('student_record_id', $this->studentRecord->id)
            ->where('requested_by', auth()->id())
            ->findOrFail($requestId);

        try {
            $submitRequest->withdraw($request, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('Request taken back.');
    }

    public function render(): View
    {
        $this->studentRecord->loadMissing('user:id,name');

        return view('livewire.portal-requests', [
            'requests' => PortalRequest::query()
                ->where('student_record_id', $this->studentRecord->id)
                ->where('requested_by', auth()->id())
                ->latest('id')
                ->get(),
            'types' => PortalRequestType::cases(),
        ]);
    }

    private function ensureAccess(PortalAccess $access): void
    {
        abort_unless($access->canRead(auth()->user(), $this->studentRecord), 403);
        abort_unless($access->areaIsOpen(PortalArea::Requests, $this->studentRecord->school_id), 404);
    }
}
