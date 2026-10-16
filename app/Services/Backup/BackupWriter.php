<?php

namespace App\Services\Backup;

use App\Exceptions\InvalidValueException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * Dump, compress, and optionally encrypt backups before storing them.
 * Remove temporary files on success or failure.
 */
class BackupWriter
{
    public function __construct(
        private DatabaseDumperRegistry $dumpers,
        private BackupCipher $cipher,
    ) {}

    /**
     * Back up the database, and the uploaded files when asked.
     *
     * @return array<int, string> the paths written to the backup disk
     */
    public function write(bool $withFiles = false): array
    {
        $stamp = now()->format('Y-m-d-His');
        $written = [$this->writeDatabase($stamp)];

        if ($withFiles) {
            $files = $this->writeFiles($stamp);

            if ($files !== null) {
                $written[] = $files;
            }
        }

        return $written;
    }

    /**
     * Dump the database and put the locked copy on the backup disk.
     */
    private function writeDatabase(string $stamp): string
    {
        $connection = (string) config('database.default');
        $dumper = $this->dumpers->forConnection($connection);

        $plain = $this->workingFile('sql');
        $squeezed = $this->workingFile('gz');

        try {
            $dumper->dumpTo($plain, (array) DB::connection($connection)->getConfig());
            $this->squeeze($plain, $squeezed);

            return $this->store($squeezed, "skuul-$stamp.sql.gz");
        } finally {
            $this->forget($plain, $squeezed);
        }
    }

    /**
     * Put the uploaded files on the backup disk.
     *
     * Returns nothing when the school has uploaded nothing yet.
     */
    private function writeFiles(string $stamp): ?string
    {
        $archive = $this->workingFile('zip');

        try {
            $count = $this->zip($archive);

            if ($count === 0) {
                return null;
            }

            return $this->store($archive, "skuul-$stamp.files.zip");
        } finally {
            $this->forget($archive);
        }
    }

    /**
     * Lock the working file if a key is set, then copy it to the backup disk.
     *
     * @throws InvalidValueException when this installation demands locked backups and has no key
     */
    private function store(string $working, string $name): string
    {
        $locked = null;

        try {
            if ($this->cipher->isConfigured()) {
                $locked = $this->workingFile('enc');
                $this->cipher->encryptFile($working, $locked);
                $working = $locked;
                $name .= '.enc';
            } elseif (config('monitoring.backup.require_encryption')) {
                throw new InvalidValueException(
                    'This installation keeps its backups locked, but no BACKUP_KEY is set. Set one, or turn the rule off.'
                );
            }

            $path = trim((string) config('monitoring.backup.path'), '/')."/$name";
            $file = fopen($working, 'rb');

            if ($file === false) {
                throw new RuntimeException('The backup could not be read back.');
            }

            try {
                if (!Storage::disk((string) config('monitoring.backup.disk'))->writeStream($path, $file)) {
                    throw new RuntimeException('The backup could not be stored.');
                }
            } finally {
                fclose($file);
            }

            return $path;
        } finally {
            if ($locked !== null) {
                $this->forget($locked);
            }
        }
    }

    /**
     * Squeeze one file, a piece at a time.
     */
    private function squeeze(string $from, string $to): void
    {
        $in = fopen($from, 'rb');
        $out = gzopen($to, 'wb6');

        if ($in === false || $out === false) {
            if (is_resource($in)) {
                fclose($in);
            }

            if (is_resource($out)) {
                gzclose($out);
            }

            throw new RuntimeException('The backup could not be squeezed.');
        }

        try {
            while (!feof($in)) {
                $piece = fread($in, 1048576);

                if ($piece === false) {
                    throw new RuntimeException('The database dump could not be read.');
                }

                if ($piece !== '' && gzwrite($out, $piece) !== strlen($piece)) {
                    throw new RuntimeException('The compressed backup could not be written.');
                }
            }
        } finally {
            fclose($in);
            gzclose($out);
        }
    }

    /**
     * Archive uploads through the configured storage disk, including remote disks.
     */
    private function zip(string $archive): int
    {
        $diskName = config('monitoring.backup.files_disk');

        if (!is_string($diskName) || trim($diskName) === '') {
            throw new InvalidValueException('Set BACKUP_FILES_DISK before requesting a file backup.');
        }

        $disk = Storage::disk($diskName);
        $excludedPaths = [
            trim((string) config('monitoring.backup.path'), '/'),
            trim((string) config('monitoring.backup.rehearsal.path'), '/'),
        ];

        if (in_array('', $excludedPaths, true)) {
            throw new InvalidValueException('Backup and rehearsal paths must be named directories for file backups.');
        }

        $files = array_values(array_filter($disk->allFiles(), function (string $file) use ($excludedPaths): bool {
            foreach ($excludedPaths as $path) {
                if ($file === $path || str_starts_with($file, $path.'/')) {
                    return false;
                }
            }

            return true;
        }));

        if ($files === []) {
            return 0;
        }

        $zip = new ZipArchive;

        if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('The file archive could not be started.');
        }

        $copies = [];
        $count = 0;

        try {
            foreach ($files as $file) {
                $local = $this->localCopy($disk, $file, $copies);

                if (!$zip->addFile($local, $file)) {
                    throw new RuntimeException("The uploaded file [$file] could not be archived.");
                }

                $count++;
            }
        } finally {
            try {
                $closed = $zip->close();
            } finally {
                $this->forget(...$copies);
            }
        }

        if (!$closed) {
            throw new RuntimeException('The file archive could not be completed.');
        }

        return $count;
    }

    /**
     * Get a path on this machine for one stored file.
     *
     * A file already on this machine is read where it lies. A file in a bucket
     * is brought down first, and removed once the archive is closed.
     *
     * @param  array<int, string>  $copies
     */
    private function localCopy(Filesystem $disk, string $file, array &$copies): string
    {
        if ($disk instanceof FilesystemAdapter) {
            $path = $disk->path($file);

            if (is_file($path)) {
                return $path;
            }
        }

        $stream = $disk->readStream($file);

        if (!is_resource($stream)) {
            throw new RuntimeException("The uploaded file [$file] could not be read.");
        }

        $copy = $this->workingFile('part');
        $copies[] = $copy;
        $handle = fopen($copy, 'wb');

        if ($handle === false) {
            fclose($stream);

            throw new RuntimeException("A working copy of [$file] could not be made.");
        }

        try {
            if (stream_copy_to_stream($stream, $handle) === false) {
                throw new RuntimeException("The uploaded file [$file] could not be copied.");
            }
        } finally {
            fclose($handle);
            fclose($stream);
        }

        return $copy;
    }

    /**
     * Make a working file this machine can write to.
     */
    private function workingFile(string $extension): string
    {
        $file = tempnam(sys_get_temp_dir(), 'skuul-backup');

        if ($file === false) {
            throw new RuntimeException('A working file for the backup could not be made.');
        }

        return "$file.$extension";
    }

    /**
     * Remove the working copies, whatever happened.
     */
    private function forget(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (is_file($path)) {
                unlink($path);
            }

            // tempnam() made the file without the extension as well.
            $stem = preg_replace('/\.[a-z]+$/', '', $path);

            if (is_string($stem) && $stem !== $path && is_file($stem)) {
                unlink($stem);
            }
        }
    }
}
