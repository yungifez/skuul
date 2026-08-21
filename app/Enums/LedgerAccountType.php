<?php

namespace App\Enums;

/**
 * The kind of account in the chart of accounts.
 *
 * The kind decides which side of the book increases the account, so the
 * application can build balanced entries from ordinary school workflows.
 */
enum LedgerAccountType: string
{
    /**
     * Something the school owns or is owed, such as cash or school fees due.
     */
    case Asset = 'asset';

    /**
     * Something the school owes, such as money held for a future term.
     */
    case Liability = 'liability';

    /**
     * What the school is worth, including opening balances.
     */
    case Equity = 'equity';

    /**
     * Money the school earns, such as tuition.
     */
    case Income = 'income';

    /**
     * Money the school spends, such as salaries or supplies.
     */
    case Expense = 'expense';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Asset => 'Asset',
            self::Liability => 'Liability',
            self::Equity => 'Equity',
            self::Income => 'Income',
            self::Expense => 'Expense',
        };
    }

    /**
     * Get the side that increases this kind of account.
     */
    public function normalBalance(): string
    {
        return match ($this) {
            self::Asset, self::Expense => 'debit',
            self::Liability, self::Equity, self::Income => 'credit',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }
}
