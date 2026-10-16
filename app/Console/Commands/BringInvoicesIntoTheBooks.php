<?php

namespace App\Console\Commands;

use App\Actions\Finance\BringInvoiceIntoTheBooks;
use App\Exceptions\InvalidValueException;
use App\Models\FeeInvoice;
use Illuminate\Console\Command;

/**
 * Post legacy invoices missing from the ledger. Repeated runs skip invoices already posted.
 */
class BringInvoicesIntoTheBooks extends Command
{
    /**
     * @var string
     */
    protected $signature = 'skuul:bring-invoices-into-books {--school= : Only this school id}';

    /**
     * @var string
     */
    protected $description = 'Put invoices raised before the ledger existed on their learners\' accounts';

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
