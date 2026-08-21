<?php

namespace App\Console\Commands;

use App\Actions\Notice\PublishNotice;
use App\Enums\NoticeStatus;
use App\Models\Notice;
use Illuminate\Console\Command;

/**
 * Publish notices whose day has come and retire the ones that ran out.
 *
 * A school office should not have to be at a screen at eight in the morning
 * for a notice to appear.
 */
class ProcessNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:process-notices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled notices and expire finished ones';

    /**
     * Execute the console command.
     */
    public function handle(PublishNotice $publisher): int
    {
        $published = 0;

        $due = Notice::query()
            ->where('status', NoticeStatus::Scheduled)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', now())
            ->get();

        foreach ($due as $notice) {
            $publisher->publish($notice);
            $published++;
        }

        $expired = 0;

        $finished = Notice::query()
            ->where('status', NoticeStatus::Published)
            ->whereNotNull('stop_date')
            ->whereDate('stop_date', '<', now()->toDateString())
            ->get();

        foreach ($finished as $notice) {
            $publisher->expire($notice);
            $expired++;
        }

        $this->info("Published $published notices and expired $expired.");

        return self::SUCCESS;
    }
}
