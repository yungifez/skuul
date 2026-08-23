<?php

namespace App\Services\Backup;

use App\Exceptions\InvalidValueException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * Take one backup and put it where the school keeps them.
 *
 * The database is dumped, squeezed, locked, and only then copied to the backup
 * disk. Nothing half-written is ever left there, and the working copies are
 * removed whether the backup worked or not.
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
            $dumper->dumpTo($plain, (array) config("database.connections.$connection"));
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

            Storage::disk((string) config('monitoring.backup.disk'))->writeStream($path, $file);
            fclose($file);

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
            throw new RuntimeException('The backup could not be squeezed.');
        }

        while (!feof($in)) {
            $piece = fread($in, 1048576);

            if ($piece === false || $piece === '') {
                continue;
            }

            gzwrite($out, $piece);
        }

        fclose($in);
        gzclose($out);
    }

    /**
     * Put the uploaded files into one archive, and say how many went in.
     *
     * The files are read through the storage disk, so this works whether the
     * school keeps them on this machine or in a bucket somewhere else.
     */
    private function zip(string $archive): int
    {
        $disk = Storage::disk((string) config('monitoring.backup.files_disk'));
        $files = $disk->allFiles();

        if ($files === []) {
            return 0;
        }

        $zip = new ZipArchive;

        if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('The file archive could not be started.');
        }

        $copies = [];
        $count = 0;

        foreach ($files as $file) {
            $local = $this->localCopy($disk, $file, $copies);

            if ($local === null) {
                continue;
            }

            $zip->addFile($local, $file);
            $count++;
        }

        $zip->close();

        foreach ($copies as $copy) {
            $this->forget($copy);
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
    private function localCopy(Filesystem $disk, string $file, array &$copies): ?string
    {
        if ($disk instanceof FilesystemAdapter) {
            $path = $disk->path($file);

            if (is_file($path)) {
                return $path;
            }
        }

        $stream = $disk->readStream($file);

        if ($stream === null) {
            return null;
        }

        $copy = $this->workingFile('part');
        $handle = fopen($copy, 'wb');

        if ($handle === false) {
            fclose($stream);

            return null;
        }

        stream_copy_to_stream($stream, $handle);
        fclose($handle);
        fclose($stream);
        $copies[] = $copy;

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
