<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountStatusChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The applicant whose account application was updated.
     */
    public User $applicant;

    /**
     * The new status.
     */
    public string $status;

    /**
     * Optional reason or message for status change.
     *
     * @var string
     */
    public ?string $reason;

    /**
     * Create a new event instance.
     *
     * @param string $reason
     */
    public function __construct(User $applicant, string $status, ?string $reason = '')
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
