<?php

namespace App\Http\Controllers;

use App\Actions\Portal\UpdatePortalNotificationPreferences;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\UpdateNoticeNotificationPreferenceRequest;
use App\Http\Requests\UpdatePortalNotificationPreferencesRequest;
use App\Models\NoticeNotificationPreference;
use App\Services\Portal\PortalAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeNotificationPreferenceController extends Controller
{
    public function edit(Request $request): View
    {
        $preference = NoticeNotificationPreference::firstOrCreate(['user_id' => $request->user()->id, 'school_id' => current_school_id()]);

        return view('pages.notice.preferences', compact('preference'));
    }

    public function update(UpdateNoticeNotificationPreferenceRequest $request): RedirectResponse
    {
        NoticeNotificationPreference::updateOrCreate(['user_id' => $request->user()->id, 'school_id' => current_school_id()], $request->validated());

        return back()->with('success', 'Notice delivery preferences saved.');
    }

    public function portalEdit(Request $request, PortalAccess $portalAccess): View
    {
        $schools = $portalAccess->notificationSchoolsFor($request->user());

        abort_if($schools->isEmpty(), 404);

        $preferences = NoticeNotificationPreference::query()
            ->whereBelongsTo($request->user())
            ->whereIn('school_id', $schools->pluck('id'))
            ->get()
            ->keyBy('school_id');

        return view('pages.portal.notification-preferences', compact('schools', 'preferences'));
    }

    public function portalUpdate(
        UpdatePortalNotificationPreferencesRequest $request,
        UpdatePortalNotificationPreferences $updatePreferences,
    ): RedirectResponse {
        try {
            $updatePreferences->update($request->user(), $request->validated('preferences'));
        } catch (InvalidValueException $exception) {
            return back()->withErrors(['preferences' => $exception->getMessage()])->withInput();
        }

        return back()->with('success', 'Your notice email preferences were saved.');
    }
}
