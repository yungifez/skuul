<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AccountStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The applicant whose account application was updated
     *
     * @var User
     */
    public User $applicant;

    /**
     * The new status
     *
     * @var string
     */
    public string $status;

    /**
     * Optional reason or message for status change
     *
     * @var string
     */
    public ?string $reason;

   /**
    * Create a new event instance
    *
    * @param User $applicant
    * @param string $status
    * @param string $reason
    */
    public function __construct(User $applicant,string $status, ?string $reason = "" )
    {
        $this->applicant = $applicant;
        $this->status = $status;
        $this->reason = $reason;
    }

    // /**
    //  * Get the channels the event should broadcast on.
    //  *
    //  * @return \Illuminate\Broadcasting\Channel|array
    //  */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    // }
}
