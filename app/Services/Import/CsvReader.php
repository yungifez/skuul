<?php

namespace App\Services\Import;

use App\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Storage;

/**
 * Turn a CSV file into rows keyed by their column names.
 *
 * Column names are trimmed and lowercased, so a heading of "Email " and one
 * of "email" mean the same thing.
 */
class CsvReader
{
    /**
     * Read a file on the given disk.
     *
     *
     * @return array<int, array<string, string|null>>
     *
     * @throws InvalidValueException when the file is missing or has no heading row
     */
    public function read(string $path, string $disk = 'local'): array
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new InvalidValueException("There is no file at $path.");
        }

        return $this->parse((string) Storage::disk($disk)->get($path));
    }

    /**
     * Read CSV text.
     *
     *
     * @return array<int, array<string, string|null>>
     *
     * @throws InvalidValueException when the text has no heading row
     */
    public function parse(string $contents): array
    {
        // Excel starts a UTF-8 file with a byte order mark, which would
        // otherwise hide the first column name.
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;

        // Older Excel saves a plain CSV in the Windows character set. Read as
        // UTF-8, an accented name would be garbled or refused by the database.
        if (!mb_check_encoding($contents, 'UTF-8')) {
            $contents = mb_convert_encoding($contents, 'UTF-8', 'Windows-1252');
        }

        // Read record by record, not line by line: a quoted cell can hold a
        // line break, such as an address typed on two lines.
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, trim($contents));
        rewind($stream);

        $headingRow = fgetcsv($stream, escape: '\\');

        if ($headingRow === false || $this->isBlank($headingRow)) {
            fclose($stream);

            throw new InvalidValueException('The file has no heading row.');
        }

        $headings = array_map(fn (?string $heading): string => strtolower(trim((string) $heading)), $headingRow);
        $rows = [];

        while (($values = fgetcsv($stream, escape: '\\')) !== false) {
            if ($this->isBlank($values)) {
                continue;
            }

            $row = [];

            foreach ($headings as $index => $heading) {
                $value = $values[$index] ?? null;
                $value = $value === null ? null : trim($value);
                $row[$heading] = $value === '' ? null : $value;
            }

            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }

    /**
     * Check whether a record holds nothing but spaces.
     *
     * @param  array<int, string|null>  $values
     */
    private function isBlank(array $values): bool
    {
        return trim(implode('', array_map(fn (?string $value): string => (string) $value, $values))) === '';
    }
}
