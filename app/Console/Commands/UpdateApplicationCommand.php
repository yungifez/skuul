<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Throwable;

/**
 * Move this install to the newest Skuul release on GitHub.
 *
 * The install must be a git checkout with no local changes. The command
 * backs up first, and it stops at a new major version, which can need
 * manual steps from its release notes.
 */
class UpdateApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skuul:update {--check : Only say whether a newer release exists} {--no-backup : Do not take a backup first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Skuul to the newest release on GitHub';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $currentVersion = $this->currentVersion();

        if ($currentVersion === null) {
            $this->error('This install is not a git checkout of a Skuul release. Update it by hand.');

            return self::FAILURE;
        }

        try {
            $release = $this->latestRelease();
        } catch (Throwable $exception) {
            $this->error('GitHub did not answer: '.$exception->getMessage());

            return self::FAILURE;
        }

        $newVersion = $release['tag_name'];

        if (version_compare($this->normalize($newVersion), $this->normalize($currentVersion), '<=')) {
            $this->info("Skuul $currentVersion is the newest release.");

            return self::SUCCESS;
        }

        $this->info("Skuul $newVersion is out. This install runs $currentVersion.");
        $this->line("Release notes: {$release['html_url']}");

        if ($this->option('check')) {
            return self::SUCCESS;
        }

        if ($this->majorVersion($newVersion) !== $this->majorVersion($currentVersion)) {
            $this->error("$newVersion is a new major version. Follow its release notes to update.");

            return self::FAILURE;
        }

        if ($this->git(['status', '--porcelain', '--untracked-files=no'])->output() !== '') {
            $this->error('This checkout has local changes. Commit or remove them, then update again.');

            return self::FAILURE;
        }

        if (!$this->git(['fetch', '--tags', '--force', 'origin'])->successful()) {
            $this->error('The release could not be downloaded. Nothing was changed.');

            return self::FAILURE;
        }

        if (!$this->option('no-backup') && $this->call('skuul:backup', ['--with-files' => true]) !== self::SUCCESS) {
            $this->error('The backup failed. Nothing was changed.');

            return self::FAILURE;
        }

        $this->call('down');

        foreach ($this->steps($newVersion) as $label => $command) {
            $this->line("{$label}…");

            $result = Process::path(base_path())->timeout(1800)->run($command);

            if ($result->failed()) {
                $this->error("$label failed.");
                $this->line(trim($result->errorOutput() ?: $result->output()));
                $this->warn("The site stays in maintenance mode. To go back, run `git checkout $currentVersion` and `composer install --no-dev`, restore the backup, then run `php artisan up`.");

                return self::FAILURE;
            }
        }

        $this->call('up');
        $this->info("Skuul is now on $newVersion.");

        return self::SUCCESS;
    }

    /**
     * The commands that move the checkout to the release, in order.
     *
     * Artisan runs in a new process so that the new release's code does the work.
     *
     * @return array<string, list<string>>
     */
    private function steps(string $version): array
    {
        return [
            "Checking out $version" => ['git', 'checkout', '--quiet', $version],
            'Installing PHP packages' => ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction'],
            'Installing front-end packages' => ['npm', 'ci'],
            'Building the front end' => ['npm', 'run', 'build'],
            'Updating the database' => [PHP_BINARY, 'artisan', 'migrate', '--force'],
            'Updating roles and permissions' => [PHP_BINARY, 'artisan', 'db:seed', '--class=RunInProductionSeeder', '--force'],
            'Caching configuration, routes and views' => [PHP_BINARY, 'artisan', 'optimize'],
            'Restarting the queue workers' => [PHP_BINARY, 'artisan', 'queue:restart'],
        ];
    }

    /**
     * The release tag this checkout is on, or null when it is not on one.
     */
    private function currentVersion(): ?string
    {
        $result = $this->git(['describe', '--tags', '--abbrev=0']);

        return $result->successful() ? trim($result->output()) : null;
    }

    /**
     * The newest published release, which GitHub never gives as a draft or prerelease.
     *
     * @return array{tag_name: string, html_url: string}
     */
    private function latestRelease(): array
    {
        $repository = config('release.update_repository');

        return Http::acceptJson()
            ->withUserAgent('skuul-updater')
            ->timeout(15)
            ->get("https://api.github.com/repos/$repository/releases/latest")
            ->throw()
            ->json();
    }

    /**
     * Run git in the application's directory.
     *
     * @param  list<string>  $arguments
     */
    private function git(array $arguments): ProcessResult
    {
        return Process::path(base_path())->run(['git', ...$arguments]);
    }

    private function normalize(string $version): string
    {
        return ltrim(strtolower(trim($version)), 'v');
    }

    private function majorVersion(string $version): string
    {
        return explode('.', $this->normalize($version))[0];
    }
}
