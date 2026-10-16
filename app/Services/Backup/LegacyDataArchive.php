<?php

namespace App\Services\Backup;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Archive removed V2 rows as JSON lines in storage/app/legacy-v2.
 * These files preserve raw rows; they are not a database backup or a V3 conversion.
 */
class LegacyDataArchive
{
    /**
     * Append archived rows so migration retries preserve earlier exports.
     * Skip tables that no longer exist.
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
