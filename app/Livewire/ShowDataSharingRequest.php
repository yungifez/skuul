<?php

namespace App\Livewire;

use App\Actions\Sharing\FulfilDataSharingRequest;
use App\Actions\Sharing\RequestDataSharing;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\DataSharingRequest;
use App\Models\TransferPackage;
use App\Services\Sharing\TransferPackageReader;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One request to share a learner's records between two schools.
 *
 * The school that holds the records answers it and hands the copy over. The
 * school that asked takes the copy in. Each step is its own deliberate act.
 */
class ShowDataSharingRequest extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public DataSharingRequest $sharingRequest;

    public string $note = '';

    public function mount(): void
    {
        Gate::authorize('view', $this->sharingRequest);
    }

    public function decide(string $status, RequestDataSharing $requestDataSharing): void
    {
        Gate::authorize('decide', $this->sharingRequest);

        $nextStatus = DataSharingStatus::tryFrom($status);
        abort_if($nextStatus === null || $nextStatus === DataSharingStatus::Fulfilled, 422);

        $this->validate(['note' => ['nullable', 'string', 'max:500']]);

        if ($nextStatus === DataSharingStatus::Approved && $this->refuseUnreadableCategories('approve')) {
            return;
        }

        try {
            $this->sharingRequest = $requestDataSharing->changeStatus(
                request: $this->sharingRequest,
                status: $nextStatus,
                actor: auth()->user(),
                note: trim($this->note) === '' ? null : trim($this->note),
            );
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->reset('note');
        $this->notify("Request {$nextStatus->label()}.");
    }

    public function fulfil(FulfilDataSharingRequest $fulfilDataSharingRequest): void
    {
        Gate::authorize('fulfil', $this->sharingRequest);

        if ($this->refuseUnreadableCategories('hand over')) {
            return;
        }

        try {
            $fulfilDataSharingRequest->fulfil($this->sharingRequest, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->sharingRequest->refresh();
        $this->notify('Records handed over. The other school still has to take them in.');
    }

    public function receive(FulfilDataSharingRequest $fulfilDataSharingRequest): void
    {
        $package = $this->package();

        abort_if($package === null, 404);
        abort_unless(auth()->user()?->can('request data sharing'), 403);
        abort_unless($package->destination_school_id === current_school_id(), 403);

        try {
            $fulfilDataSharingRequest->receive($package, actor: auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify('Records taken in.');
    }

    public function render(TransferPackageReader $reader): View
    {
        $this->sharingRequest->loadMissing([
            'requestingSchool:id,name',
            'holdingSchool:id,name',
            'studentRecord:id,school_id,admission_number,user_id',
            'studentRecord.user:id,name',
            'requestedBy:id,name',
            'decidedBy:id,name',
        ]);

        $school = current_school_id();
        $decisions = array_values(array_filter(
            $this->sharingRequest->status->allowedNext(),
            fn (DataSharingStatus $status): bool => $status !== DataSharingStatus::Fulfilled,
        ));

        $package = $this->package();
        $canRead = $package?->wasReceived() && Gate::allows('readRecords', $this->sharingRequest);
        $hidden = $canRead ? $this->sharingRequest->categoriesUnreadableBy(auth()->user()) : [];

        return view('livewire.show-data-sharing-request', [
            'package' => $package,
            'sections' => $canRead ? $reader->sections($package, $hidden) : [],
            'hiddenCategories' => $hidden,
            'isHolder' => $school === $this->sharingRequest->holding_school_id,
            'isRequester' => $school === $this->sharingRequest->requesting_school_id,
            'decisions' => Gate::allows('decide', $this->sharingRequest) ? $decisions : [],
            'canFulfil' => Gate::allows('fulfil', $this->sharingRequest) && $this->sharingRequest->isUsable(),
        ]);
    }

    /**
     * Refuse to share a restricted category the person may not read here.
     *
     * A person who cannot open a learner's safeguarding cases at their own
     * campus must not send them to another one.
     */
    private function refuseUnreadableCategories(string $verb): bool
    {
        $unreadable = $this->sharingRequest->categoriesUnreadableBy(auth()->user());

        if ($unreadable === []) {
            return false;
        }

        $names = collect($unreadable)->map(fn (DataCategory $category): string => $category->label())->join(', ', ' and ');
        $this->notify("You cannot {$verb} {$names}. You need the permission to read them at this campus.", 'danger');

        return true;
    }

    private function package(): ?TransferPackage
    {
        return TransferPackage::query()
            ->where('data_sharing_request_id', $this->sharingRequest->id)
            ->latest('id')
            ->first();
    }
}
