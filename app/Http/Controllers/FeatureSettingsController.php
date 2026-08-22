<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Http\Requests\UpdateFeatureSettingsRequest;
use App\Services\Feature\FeatureManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureSettingsController extends Controller
{
    public function __construct(private FeatureManager $features) {}

    public function edit(): View
    {
        $this->authorize('update', current_school());

        return view('pages.school.features', ['features' => $this->features->all()]);
    }

    public function update(UpdateFeatureSettingsRequest $request): RedirectResponse
    {
        $this->authorize('update', current_school());

        foreach (Feature::cases() as $feature) {
            $enabled = $request->boolean('features.'.$feature->value);
            $enabled ? $this->features->enable($feature) : $this->features->disable($feature);
        }

        return to_route('schools.features.edit')->with('success', 'School features updated.');
    }
}
