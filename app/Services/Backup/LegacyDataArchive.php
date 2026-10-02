<?php

namespace App\Services\Backup;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Keep the rows an upgrade removes.
 *
 * Version 3 replaced the old exam records, grade systems and gradebook. A
 * school that upgrades still owns its old results, so each migration that
 * removes them first writes them to storage/app/legacy-v2, one JSON row per
 * line. A new installation has nothing to keep and gets no files.
 */
class LegacyDataArchive
{
    /**
     * Write the rows of the query to the named file and return its path.
     *
     * A table that is gone already has nothing left to keep.
     *
     * The file is added to, never replaced, so running a migration again
     * keeps what the first run wrote.
     */
    public static function keep(string $name, Builder $rows): ?string
    {
        if (!Schema::hasTable((string) $rows->from) || !$rows->exists()) {
            return null;
        }

        $directory = storage_path('app/legacy-v2');
        File::ensureDirectoryExists($directory);

        $path = "$directory/$name.jsonl";
        $file = fopen($path, 'a');

        foreach ($rows->cursor() as $row) {
            fwrite($file, json_encode($row, JSON_UNESCAPED_UNICODE).PHP_EOL);
        }

        fclose($file);

        return $path;
    }
}
