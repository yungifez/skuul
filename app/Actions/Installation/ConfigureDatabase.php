<?php

namespace App\Actions\Installation;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ConfigureDatabase
{
    /**
     * @return array{driver: string, host: string, port: string, database: string, username: string}
     */
    public function currentSettings(): array
    {
        $driver = (string) config('database.default');
        $connection = (array) config("database.connections.{$driver}", []);

        return [
            'driver' => $driver,
            'host' => (string) ($connection['host'] ?? ''),
            'port' => (string) ($connection['port'] ?? ''),
            'database' => (string) ($connection['database'] ?? ''),
            'username' => (string) ($connection['username'] ?? ''),
        ];
    }

    /**
     * Test submitted settings without changing the application connection.
     *
     * @param  array{driver: string, host?: string|null, port?: int|null, database: string, username?: string|null, password?: string|null}  $data
     */
    public function test(array $data): void
    {
        DB::purge('installer');
        config(['database.connections.installer' => $this->connection($data)]);
        DB::connection('installer')->getPdo();
    }

    /**
     * Save settings, switch the current process to them, and run migrations.
     *
     * @param  array{driver: string, host?: string|null, port?: int|null, database: string, username?: string|null, password?: string|null}  $data
     */
    public function migrate(array $data): void
    {
        $this->test($data);
        $this->writeEnvironment($data);
        Artisan::call('config:clear');

        $connection = $this->connection($data);
        config([
            'database.default' => $data['driver'],
            "database.connections.{$data['driver']}" => $connection,
        ]);
        DB::purge($data['driver']);

        if (Artisan::call('migrate', ['--force' => true]) !== 0) {
            throw new \RuntimeException('The database migrations did not complete.');
        }
    }

    /**
     * @param  array{driver: string, host?: string|null, port?: int|null, database: string, username?: string|null, password?: string|null}  $data
     * @return array<string, mixed>
     */
    private function connection(array $data): array
    {
        $connection = (array) config("database.connections.{$data['driver']}", []);
        $connection['driver'] = $data['driver'];
        $connection['database'] = $data['database'];
        $connection['username'] = $data['username'] ?? '';
        $connection['password'] = $data['password'] ?? '';
        $connection['url'] = null;

        if ($data['driver'] !== 'sqlite') {
            $connection['host'] = $data['host'] ?? '';
            $connection['port'] = (string) ($data['port'] ?? '');
        }

        return $connection;
    }

    /**
     * @param  array{driver: string, host?: string|null, port?: int|null, database: string, username?: string|null, password?: string|null}  $data
     */
    private function writeEnvironment(array $data): void
    {
        $path = app()->environmentFilePath();
        $contents = File::exists($path) ? File::get($path) : '';
        $values = [
            'DB_CONNECTION' => $data['driver'],
            'DB_HOST' => $data['host'] ?? '',
            'DB_PORT' => isset($data['port']) ? (string) $data['port'] : '',
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'] ?? '',
            'DB_PASSWORD' => $data['password'] ?? '',
        ];

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->environmentValue($value);
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

            if (preg_match($pattern, $contents) === 1) {
                $contents = (string) preg_replace($pattern, $line, $contents);
            } else {
                $contents = rtrim($contents, "\r\n")."\n{$line}\n";
            }
        }

        File::put($path, $contents);
    }

    private function environmentValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }
}
