<?php

namespace App\Livewire;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Services\Feature\FeatureManager;
use App\Services\Portal\PortalAccess;
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

    /**
     * Which family pages the campus shows, keyed by portal area.
     *
     * @var array<string, bool>
     */
    public array $portalAreas = [];

    public function mount(FeatureManager $features): void
    {
        Gate::authorize('update', current_school());

        $this->enabled = $features->all();
        $this->portalAreas = $this->currentPortalAreas($features);
    }

    /**
     * Show or hide one family page, keeping the choices for the others.
     */
    public function updatedPortalAreas(mixed $value, string $key): void
    {
        Gate::authorize('update', current_school());

        $features = app(FeatureManager::class);
        $area = PortalArea::tryFrom($key);

        if ($area === null || $features->disabled(Feature::Portal)) {
            $this->portalAreas = $this->currentPortalAreas($features);

            return;
        }

        $isShown = (bool) $value;
        $choices = array_merge($this->currentPortalAreas($features), [$area->value => $isShown]);
        $features->enable(Feature::Portal, actor: auth()->user(), config: $choices);
        $this->portalAreas = $choices;

        $this->notify($area->label().($isShown ? ' is shown to families.' : ' is hidden from families.'));
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
            'areas' => PortalArea::cases(),
            'areaNeeds' => PortalAccess::AREA_FEATURES,
        ]);
    }

    /**
     * Read whether each family page is shown. A page nobody chose is shown.
     *
     * @return array<string, bool>
     */
    private function currentPortalAreas(FeatureManager $features): array
    {
        $areas = [];

        foreach (PortalArea::cases() as $area) {
            $areas[$area->value] = (bool) $features->config(Feature::Portal, $area->value, true);
        }

        return $areas;
    }
}
