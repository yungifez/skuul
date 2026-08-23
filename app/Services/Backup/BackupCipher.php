<?php

namespace App\Services\Backup;

use App\Exceptions\InvalidValueException;
use RuntimeException;

/**
 * Lock and unlock a backup file.
 *
 * A backup holds every mark, every fee, and every safeguarding note the school
 * has. It is kept away from the servers that made it, which means it is kept
 * somewhere the school controls less, so it leaves the machine locked.
 *
 * The file is read and written a piece at a time: a backup is far larger than
 * the memory a worker is given. Each piece is sealed with its own starting
 * value, and the whole file is signed, so a file that was changed on the way
 * is refused rather than half-restored.
 */
class BackupCipher
{
    /**
     * The first bytes of every locked file, so the format is recognisable.
     */
    private const MAGIC = "SKUULBK1\n";

    /**
     * How much is sealed at a time.
     */
    private const CHUNK = 1048576;

    /**
     * How long the signature at the end is.
     */
    private const SIGNATURE = 32;

    /**
     * Check whether this installation has a backup key.
     */
    public function isConfigured(): bool
    {
        $key = config('monitoring.backup.key');

        return is_string($key) && $key !== '';
    }

    /**
     * Lock one file into another.
     *
     * @throws InvalidValueException when no key is set
     */
    public function encryptFile(string $from, string $to): void
    {
        [$sealing, $signing, $salt] = $this->keys();

        $in = $this->open($from, 'rb');
        $out = $this->open($to, 'wb');

        $header = self::MAGIC.$salt;
        fwrite($out, $header);
        $signature = hash_init('sha256', HASH_HMAC, $signing);
        hash_update($signature, $header);

        while (!feof($in)) {
            $piece = fread($in, self::CHUNK);

            if ($piece === false || $piece === '') {
                continue;
            }

            $start = random_bytes(16);
            $sealed = openssl_encrypt($piece, 'aes-256-cbc', $sealing, OPENSSL_RAW_DATA, $start);

            if ($sealed === false) {
                throw new RuntimeException('The backup could not be locked.');
            }

            $frame = pack('N', strlen($sealed)).$start.$sealed;
            fwrite($out, $frame);
            hash_update($signature, $frame);
        }

        fwrite($out, hash_final($signature, true));
        fclose($in);
        fclose($out);
    }

    /**
     * Unlock one file into another.
     *
     * @throws InvalidValueException when the key is wrong or the file was changed
     */
    public function decryptFile(string $from, string $to): void
    {
        $size = filesize($from);

        if ($size === false || $size < strlen(self::MAGIC) + 16 + self::SIGNATURE) {
            throw new InvalidValueException('This is not a backup this application wrote.');
        }

        $in = $this->open($from, 'rb');
        $header = (string) fread($in, strlen(self::MAGIC) + 16);

        if (!str_starts_with($header, self::MAGIC)) {
            fclose($in);

            throw new InvalidValueException('This is not a backup this application wrote.');
        }

        $salt = substr($header, strlen(self::MAGIC));
        [$sealing, $signing] = $this->keys($salt);

        $this->checkSignature($from, $size, $signing);

        $out = $this->open($to, 'wb');
        $body = $size - self::SIGNATURE;

        while (ftell($in) < $body) {
            $length = unpack('N', (string) fread($in, 4));

            if ($length === false) {
                break;
            }

            $start = (string) fread($in, 16);
            $sealed = (string) fread($in, $length[1]);
            $piece = openssl_decrypt($sealed, 'aes-256-cbc', $sealing, OPENSSL_RAW_DATA, $start);

            if ($piece === false) {
                fclose($in);
                fclose($out);

                throw new InvalidValueException('This backup cannot be unlocked with this key.');
            }

            fwrite($out, $piece);
        }

        fclose($in);
        fclose($out);
    }

    /**
     * Refuse a file that is not signed with this installation's key.
     *
     * The signature is checked before anything is unlocked, so a changed file
     * never becomes a half-restored database.
     *
     * @throws InvalidValueException when the signature does not match
     */
    private function checkSignature(string $path, int $size, string $signing): void
    {
        $file = $this->open($path, 'rb');
        $signature = hash_init('sha256', HASH_HMAC, $signing);
        $left = $size - self::SIGNATURE;

        while ($left > 0) {
            $piece = (string) fread($file, min(self::CHUNK, $left));
            hash_update($signature, $piece);
            $left -= strlen($piece);
        }

        $written = (string) fread($file, self::SIGNATURE);
        fclose($file);

        if (!hash_equals(hash_final($signature, true), $written)) {
            throw new InvalidValueException('This backup was changed after it was written, so it was refused.');
        }
    }

    /**
     * Work out the two keys this file uses, one to seal and one to sign.
     *
     * @return array{0: string, 1: string, 2: string}
     *
     * @throws InvalidValueException when no key is set
     */
    private function keys(?string $salt = null): array
    {
        $salt ??= random_bytes(16);
        $key = $this->key();

        return [
            hash_hkdf('sha256', $key, 32, 'skuul-backup-sealing', $salt),
            hash_hkdf('sha256', $key, 32, 'skuul-backup-signing', $salt),
            $salt,
        ];
    }

    /**
     * Get the key this installation locks its backups with.
     *
     * @throws InvalidValueException when no key is set
     */
    private function key(): string
    {
        $key = config('monitoring.backup.key');

        if (!is_string($key) || $key === '') {
            throw new InvalidValueException('No backup key is set. Set BACKUP_KEY before writing a backup.');
        }

        return str_starts_with($key, 'base64:') ? (string) base64_decode(substr($key, 7), true) : $key;
    }

    /**
     * Open a file, or say plainly that it could not be opened.
     *
     * @return resource
     */
    private function open(string $path, string $mode)
    {
        $file = fopen($path, $mode);

        if ($file === false) {
            throw new RuntimeException("The file [$path] could not be opened.");
        }

        return $file;
    }
}
