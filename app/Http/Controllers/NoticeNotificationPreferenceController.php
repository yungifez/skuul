<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNoticeNotificationPreferenceRequest;
use App\Models\NoticeNotificationPreference;
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
}
