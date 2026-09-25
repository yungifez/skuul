<?php

namespace App\Livewire;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Exceptions\InvalidValueException;
use App\Services\Portal\PortalRequestInbox as PortalRequestInboxService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PortalRequestInbox extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $type = '';

    /** @var array<int, string> */
    public array $statusesByRequest = [];

    /** @var array<int, string> */
    public array $responsesByRequest = [];

    public ?string $feedback = null;

    protected PortalRequestInboxService $inbox;

    protected SubmitPortalRequest $submitRequest;

    public function boot(PortalRequestInboxService $inbox, SubmitPortalRequest $submitRequest): void
    {
        Gate::authorize('read portal request');

        $this->inbox = $inbox;
        $this->submitRequest = $submitRequest;
    }

    public function mount(): void
    {
        $selectedStatus = PortalRequestStatus::tryFrom($this->status);
        $selectedType = PortalRequestType::tryFrom($this->type);
        $this->status = $selectedStatus === null ? '' : $selectedStatus->value;
        $this->type = $selectedType === null ? '' : $selectedType->value;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->type = '';
        $this->resetPage();
    }

    public function changeStatus(int $requestId): void
    {
        $this->feedback = null;
        $request = $this->inbox->request($requestId);
        Gate::authorize('answer', $request);

        $this->statusesByRequest[$requestId] ??= $request->status->isOpen()
            ? $request->status->allowedNext()[0]->value
            : $request->status->value;

        $statusField = 'statusesByRequest.'.$requestId;
        $responseField = 'responsesByRequest.'.$requestId;
        $validated = $this->validate([
            $statusField => ['required', Rule::enum(PortalRequestStatus::class)],
            $responseField => [
                'nullable',
                'string',
                'max:2000',
                'required_if:'.$statusField.','.PortalRequestStatus::Answered->value,
            ],
        ], [
            $responseField.'.required_if' => 'An answered request must carry the answer.',
        ]);
        $nextStatus = PortalRequestStatus::from($validated['statusesByRequest'][$requestId]);

        try {
            $this->submitRequest->changeStatus(
                request: $request,
                status: $nextStatus,
                actor: auth()->user(),
                response: $validated['responsesByRequest'][$requestId] ?? null,
            );
        } catch (InvalidValueException $exception) {
            $this->addError('status', $exception->getMessage());

            return;
        }

        unset($this->statusesByRequest[$requestId], $this->responsesByRequest[$requestId]);
        $this->feedback = 'Request status updated to '.strtolower($nextStatus->label()).'.';
    }

    public function render(): View
    {
        return ViewFactory::make('livewire.portal-request-inbox', [
            'requests' => $this->inbox->requests($this->status, $this->type),
            'statuses' => PortalRequestStatus::cases(),
            'types' => PortalRequestType::cases(),
            'selectedStatus' => PortalRequestStatus::tryFrom($this->status),
            'selectedType' => PortalRequestType::tryFrom($this->type),
            'waitingCount' => $this->inbox->waitingCount(),
        ]);
    }
}
