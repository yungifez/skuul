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
     * @return array<int, array<string, string|null>>
     *
     * @throws InvalidValueException when the text has no heading row
     */
    public function parse(string $contents): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($contents)) ?: [];

        if ($lines === [] || $lines[0] === '') {
            throw new InvalidValueException('The file has no heading row.');
        }

        $headings = array_map(
            fn (string $heading): string => strtolower(trim($heading)),
            str_getcsv(array_shift($lines), escape: '\\')
        );

        $rows = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line, escape: '\\');
            $row = [];

            foreach ($headings as $index => $heading) {
                $value = $values[$index] ?? null;
                $value = $value === null ? null : trim($value);
                $row[$heading] = $value === '' ? null : $value;
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
