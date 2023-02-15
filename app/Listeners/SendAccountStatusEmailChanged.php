<?php

namespace App\Listeners;

use App\Events\AccountStatusChanged;
use App\Mail\ApplicationStatusChanged;
use Illuminate\Support\Facades\Mail;

class SendAccountStatusEmailChanged
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(AccountStatusChanged $event)
    {
        try {
            Mail::to($event->applicant->email)->send(new ApplicationStatusChanged($event->status, $event->reason));
        } catch (\Throwable $th) {
            report("Could not send email to $event->applicant->email");

            return;
        }
    }
}
