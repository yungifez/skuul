<?php

namespace App\Listeners;

use App\Events\AccountStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  \App\Events\AccountStatusChanged  $event
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
