<?php

namespace App\Console\Commands;

use App\Models\AccountInvitation;
use Illuminate\Console\Command;

/**
 * Revoke invitations nobody accepted before they expired.
 *
 * An expired link already fails when someone opens it. Revoking it keeps the
 * pending list honest and leaves the record for history.
 */
class PruneExpiredInvitations extends Command
{
    /**
     * @var string
     */
    protected $signature = 'skuul:prune-expired-invitations';

    /**
     * @var string
     */
    protected $description = 'Revoke account invitations that expired';

    public function handle(): int
    {
        $revoked = AccountInvitation::query()
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->where('expires_at', '<=', now())
            ->update(['revoked_at' => now()]);

        $this->info("Revoked $revoked expired invitations.");

        return self::SUCCESS;
    }
}
