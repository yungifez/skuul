<?php

namespace App\Livewire;

use App\Actions\Portal\UpdatePortalNotificationPreferences;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\NoticeNotificationPreference;
use App\Models\School;
use App\Services\Portal\PortalAccess;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Whether each school may email this person a copy of its optional notices.
 *
 * Staff see the school they work in. A family sees every campus their
 * children attend, each with its own switch. A switch saves as soon as it
 * moves.
 */
class NoticeEmailPreferences extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public bool $isPortal = false;

    /** @var array<int|string, bool> */
    public array $emailEnabled = [];

    public function mount(bool $isPortal = false): void
    {
        $this->isPortal = $isPortal;

        $schools = $this->schools();

        abort_if($schools->isEmpty(), 404);

        $saved = NoticeNotificationPreference::query()
            ->whereBelongsTo(auth()->user())
            ->whereIn('school_id', $schools->pluck('id'))
            ->pluck('email_enabled', 'school_id');

        foreach ($schools as $school) {
            $this->emailEnabled[$school->id] = (bool) ($saved[$school->id] ?? true);
        }
    }

    public function updatedEmailEnabled(mixed $value, string $key, UpdatePortalNotificationPreferences $updatePreferences): void
    {
        $school = $this->schools()->firstWhere('id', (int) $key);

        if ($school === null) {
            unset($this->emailEnabled[$key]);
            $this->notify('You can only change the setting for your own schools.', 'danger');

            return;
        }

        $isOn = (bool) $value;

        try {
            $this->isPortal
                ? $updatePreferences->update(auth()->user(), [$school->id => $isOn])
                : NoticeNotificationPreference::query()->upsert(
                    [['user_id' => auth()->id(), 'school_id' => $school->id, 'email_enabled' => $isOn]],
                    ['user_id', 'school_id'],
                    ['email_enabled'],
                );
        } catch (InvalidValueException $exception) {
            $this->emailEnabled[$school->id] = !$isOn;
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->emailEnabled[$school->id] = $isOn;
        $this->notify($isOn ? "{$school->name} will email you its notices." : "{$school->name} will not email you its notices.");
    }

    public function render(): View
    {
        return view('livewire.notice-email-preferences', [
            'schools' => $this->schools(),
        ]);
    }

    /**
     * @return Collection<int, School>
     */
    private function schools(): Collection
    {
        if ($this->isPortal) {
            return app(PortalAccess::class)->notificationSchoolsFor(auth()->user())->values()->toBase();
        }

        $school = current_school();

        return $school === null ? collect() : collect([$school]);
    }
}
