<?php

namespace App\Http\Controllers;

use App\Services\Portal\PortalAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeNotificationPreferenceController extends Controller
{
    /**
     * Show whether this school may email its notices to the signed-in person.
     */
    public function edit(): View
    {
        return view('pages.notice.preferences');
    }

    /**
     * Show the notice email choices for every campus a family can read.
     */
    public function portalEdit(Request $request, PortalAccess $portalAccess): View
    {
        abort_if($portalAccess->notificationSchoolsFor($request->user())->isEmpty(), 404);

        return view('pages.portal.notification-preferences');
    }
}
