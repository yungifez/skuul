<?php

namespace App\Services\Finance;

use App\Enums\LedgerAccountType;
use App\Models\LedgerAccount;
use App\Models\School;
use Illuminate\Support\Collection;

/**
 * The small set of accounts a school needs to start.
 *
 * A school office should never have to design a chart of accounts before it
 * can raise its first invoice, so the application creates one and names each
 * account by its purpose.
 */
class ChartOfAccounts
{
    /**
     * The accounts every school starts with.
     *
     * @var array<string, array{code: string, name: string, type: LedgerAccountType}>
     */
    private const DEFAULTS = [
        'fees_receivable' => ['code' => '1100', 'name' => 'School fees receivable', 'type' => LedgerAccountType::Asset],
        'cash' => ['code' => '1000', 'name' => 'Cash', 'type' => LedgerAccountType::Asset],
        'bank' => ['code' => '1010', 'name' => 'Bank', 'type' => LedgerAccountType::Asset],
        'unapplied_credits' => ['code' => '2100', 'name' => 'Unapplied student credits', 'type' => LedgerAccountType::Liability],
        'tuition_income' => ['code' => '4000', 'name' => 'Tuition income', 'type' => LedgerAccountType::Income],
        'other_income' => ['code' => '4100', 'name' => 'Other income', 'type' => LedgerAccountType::Income],
        'scholarships' => ['code' => '5000', 'name' => 'Scholarships and waivers', 'type' => LedgerAccountType::Expense],
        'bad_debt' => ['code' => '5100', 'name' => 'Written-off fees', 'type' => LedgerAccountType::Expense],
        'operating_expenses' => ['code' => '6000', 'name' => 'Operating expenses', 'type' => LedgerAccountType::Expense],
        'opening_balance' => ['code' => '3000', 'name' => 'Opening balance', 'type' => LedgerAccountType::Equity],

        /*
         * Campuses that keep one purse settle with each other through these
         * two accounts, so each campus's own books still balance when a debt
         * follows a learner from one campus to the other.
         */
        'due_from_campus' => ['code' => '1200', 'name' => 'Due from another campus', 'type' => LedgerAccountType::Asset],
        'due_to_campus' => ['code' => '2200', 'name' => 'Due to another campus', 'type' => LedgerAccountType::Liability],
    ];

    /**
     * Create the accounts the school is missing and return them all.
     *
     * @return Collection<string, LedgerAccount>
     */
    public function ensureFor(School|int $school): Collection
    {
        $schoolId = $school instanceof School ? $school->id : $school;
        $accounts = collect();

        foreach (self::DEFAULTS as $purpose => $account) {
            $accounts[$purpose] = LedgerAccount::firstOrCreate(
                ['school_id' => $schoolId, 'code' => $account['code']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'purpose' => $purpose,
                ],
            );
        }

        return $accounts;
    }

    /**
     * Get the account a school uses for one purpose.
     */
    public function account(string $purpose, School|int|null $school = null): LedgerAccount
    {
        $schoolId = $school instanceof School ? $school->id : ($school ?? current_school_id());

        $account = LedgerAccount::query()
            ->where('school_id', $schoolId)
            ->forPurpose($purpose)
            ->first();

        return $account ?? $this->ensureFor($schoolId)[$purpose];
    }

    /**
     * Get the purposes a school can map its workflows to.
     *
     * @return array<int, string>
     */
    public static function purposes(): array
    {
        return array_keys(self::DEFAULTS);
    }
}
