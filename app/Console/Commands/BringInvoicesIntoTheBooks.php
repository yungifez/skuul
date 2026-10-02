<?php

namespace App\Console\Commands;

use App\Actions\Finance\BringInvoiceIntoTheBooks;
use App\Exceptions\InvalidValueException;
use App\Models\FeeInvoice;
use Illuminate\Console\Command;

/**
 * Put every invoice from before the books on its learner's account.
 *
 * A school that upgrades keeps its old invoices, but none of them reached the
 * ledger. Until they do, the account screens show less owed than the bills.
 * Run once after the upgrade. Running it again does nothing new.
 */
class BringInvoicesIntoTheBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:bring-invoices-into-books {--school= : Only this school id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put invoices raised before the ledger existed on their learners\' accounts';

    /**
     * Execute the console command.
     */
    public function handle(BringInvoiceIntoTheBooks $bringIn): int
    {
        $brought = 0;
        $total = 0;

        FeeInvoice::query()
            ->whereNull('ledger_transaction_id')
            ->whereNotNull('student_record_id')
            ->when($this->option('school') !== null, fn ($query) => $query->where('school_id', (int) $this->option('school')))
            ->orderBy('id')
            ->chunkById(200, function ($invoices) use ($bringIn, &$brought, &$total): void {
                foreach ($invoices as $invoice) {
                    try {
                        $amount = $bringIn->bring($invoice);
                    } catch (InvalidValueException $exception) {
                        $this->warn("Invoice $invoice->name: {$exception->getMessage()}");

                        continue;
                    }

                    if ($amount > 0) {
                        $brought++;
                        $total += $amount;
                    }
                }
            });

        $this->info("$brought invoices put in the books, ".money_text($total / 100).' in all.');

        return self::SUCCESS;
    }
}
