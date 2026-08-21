<?php

namespace App\Enums;

/**
 * What happened to one person's copy of a notice.
 */
enum NoticeRecipientState: string
{
    /**
     * On its way. Nobody has opened it.
     */
    case Pending = 'pending';

    /**
     * It reached the person's board or inbox.
     */
    case Delivered = 'delivered';

    /**
     * The person opened it.
     */
    case Read = 'read';

    /**
     * The person put it away.
     */
    case Dismissed = 'dismissed';

    /**
     * It could not be delivered.
     */
    case Failed = 'failed';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Delivered => 'Delivered',
            self::Read => 'Read',
            self::Dismissed => 'Dismissed',
            self::Failed => 'Failed',
        };
    }
}
