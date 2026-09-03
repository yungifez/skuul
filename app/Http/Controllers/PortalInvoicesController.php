<?php

namespace App\Http\Controllers;

use App\Enums\PortalArea;
use App\Models\StudentRecord;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Show one learner's invoices and balance to the learner or their family.
 */
class PortalInvoicesController extends Controller
{
    public function __construct(private PortalAccess $access, private PortalSummary $summary) {}

    /**
     * Show the read-only financial record for one enrollment.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Invoices, $studentRecord->school_id), 404);

        $invoices = $this->summary->invoices($studentRecord);

        if ($invoices === null) {
            abort(404);
        }

        return view('pages.portal.invoices', [
            'studentRecord' => $studentRecord->load('user:id,name', 'school:id,name'),
            'invoices' => $invoices['invoices'],
            'balance' => $invoices['balance'],
            'unappliedCredit' => $invoices['unapplied_credit'],
        ]);
    }
}
