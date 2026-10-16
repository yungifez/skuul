<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Validate and save import rows. ImportRunner handles file reading and import history.
 */
interface Importer
{
    /**
     * Get the name people choose the import with.
     */
    public function key(): string;

    /**
     * Get the title to show in the interface.
     */
    public function title(): string;

    /**
     * Get the columns a file must have.
     *
     * @return array<int, string>
     */
    public function requiredColumns(): array;

    /**
     * Get the columns a file may have.
     *
     * @return array<int, string>
     */
    public function optionalColumns(): array;

    /**
     * Get the rules one row must follow.
     *
     * @return array<string, mixed>
     */
    public function rules(): array;

    /**
     * Write one checked row.
     *
     * The engine passes the record this row wrote last time, when the file
     * names one, so the same file can be imported twice without making a
     * second copy.
     *
     * @param  array<string, mixed>  $row
     */
    public function apply(array $row, ?Model $existing): Model;
}
