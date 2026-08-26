<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolOperatingProfileRequest;
use App\Models\SchoolOperatingProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolOperatingProfileController extends Controller
{
    public function edit(): View
    {
        $school = current_school();
        $this->authorize('update', $school);
        $profile = $school->operatingProfile()->firstOrCreate([], [
            'preset' => 'home_sections',
            'labels' => SchoolOperatingProfile::labelsFor('home_sections'),
        ]);

        return view('pages.school.operating-profile', compact('profile'));
    }

    public function update(UpdateSchoolOperatingProfileRequest $request): RedirectResponse
    {
        $school = current_school();
        $this->authorize('update', $school);
        $school->operatingProfile()->updateOrCreate([], $request->validated());

        if ($request->boolean('setup')) {
            return to_route('schools.setup', [current_school(), 'classes'])
                ->with('success', 'School language updated.');
        }

        return to_route('schools.settings')->with('success', 'School language updated.');
    }
}
