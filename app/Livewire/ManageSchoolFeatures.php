<?php

namespace App\Livewire;

use App\Enums\Feature;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Services\Feature\FeatureManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Turn the optional tools of the working school on or off.
 *
 * Each switch saves straight away. Turning a tool off hides it and closes
 * its pages; nothing is deleted.
 */
class ManageSchoolFeatures extends Component
{
    use DispatchesStatusNotifications;

    /** @var array<string, bool> */
    public array $enabled = [];

    public function mount(FeatureManager $features): void
    {
        Gate::authorize('update', current_school());

        $this->enabled = $features->all();
    }

    public function updatedEnabled(mixed $value, string $key): void
    {
        Gate::authorize('update', current_school());

        $feature = Feature::tryFrom($key);

        if ($feature === null) {
            unset($this->enabled[$key]);

            return;
        }

        $isOn = (bool) $value;
        $features = app(FeatureManager::class);
        $isOn ? $features->enable($feature, actor: auth()->user()) : $features->disable($feature, actor: auth()->user());
        $this->enabled[$key] = $isOn;

        $this->dispatch('school-features-changed');
        $this->notify($feature->label().($isOn ? ' turned on.' : ' turned off.'));
    }

    public function render(): View
    {
        return view('livewire.manage-school-features', [
            'groups' => Feature::grouped(),
        ]);
    }
}
