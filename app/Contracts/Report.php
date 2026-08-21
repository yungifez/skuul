<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * One report the application can build.
 *
 * A report answers a question about school data. It says what its columns are
 * and returns rows; how it is delivered is not its business.
 */
interface Report
{
    /**
     * Get the name people choose the report by.
     */
    public function key(): string;

    /**
     * Get the title to print at the top.
     */
    public function title(): string;

    /**
     * Get the column headings, in order.
     *
     * @return array<int, string>
     */
    public function columns(): array;

    /**
     * Build the rows of the report.
     *
     * @param  array<string, mixed>  $parameters
     * @return Collection<int, array<int, mixed>>
     */
    public function rows(array $parameters = []): Collection;
}
