<?php

namespace App\Http\Controllers;

use App\Models\StudentRecord;
use Illuminate\View\View;

/**
 * One student's money: what they owe, what they paid, what is held for them.
 *
 * The office needs one page that answers a parent at the counter, rather than
 * a list of invoices that each answer a piece of the question.
 */
class StudentAccountController extends Controller
{
    /**
     * Show everything the school knows about one student's account.
     */
    public function show(StudentRecord $studentRecord): View
    {
        abort_unless(
            auth()->user()?->can('read fee invoice') === true
                && $studentRecord->school_id === current_school_id(),
            403,
        );

        $studentRecord->loadMissing('user');

        return view('pages.fee.account.show', ['enrollment' => $studentRecord]);
    }
}
