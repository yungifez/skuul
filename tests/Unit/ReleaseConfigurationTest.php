<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use Tests\CreatesApplication;

class ReleaseConfigurationTest extends TestCase
{
    public function test_application_defaults_do_not_share_the_database_that_tests_reset(): void
    {
        $this->assertSame('skuul', $this->environmentValue('.env.example', 'DB_DATABASE'));
        $this->assertSame('testing', $this->environmentValue('.env.testing', 'DB_DATABASE'));
        $this->assertSame('false', $this->environmentValue('.env.example', 'DEMO_MODE'));

        $compose = Yaml::parseFile($this->path('docker-compose.yml'));

        foreach ($compose['services']['laravel.test']['volumes'] as $volume) {
            $this->assertStringStartsNotWith('../', $volume, 'Sail must not require a sibling project checkout.');
        }
    }

    public function test_phpunit_overrides_database_credentials_and_urls_in_both_environment_sources(): void
    {
        $configuration = simplexml_load_file($this->path('phpunit.xml'));
        $this->assertNotFalse($configuration);

        foreach (['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DATABASE_URL', 'DEMO_MODE'] as $name) {
            $expected = $this->environmentValue('.env.testing', $name);

            foreach (['env', 'server'] as $source) {
                $entries = $configuration->xpath("/phpunit/php/{$source}[@name='{$name}']");
                $this->assertCount(1, $entries, "Missing PHPUnit {$source} override for {$name}.");
                $this->assertSame($expected, (string) $entries[0]['value']);

                if ($source === 'env') {
                    $this->assertSame('true', (string) $entries[0]['force']);
                }
            }
        }

        $this->assertSame('', $this->environmentValue('.env.testing', 'DATABASE_URL'));
    }

    public function test_cached_configuration_is_refused_before_the_test_application_boots(): void
    {
        $cache = tempnam(sys_get_temp_dir(), 'skuul-test-config-');
        $this->assertIsString($cache);
        file_put_contents($cache, '<?php throw new LogicException("The application loaded cached configuration.");');
        $previousServer = $_SERVER['APP_CONFIG_CACHE'] ?? null;
        $previousEnvironment = $_ENV['APP_CONFIG_CACHE'] ?? null;
        $_SERVER['APP_CONFIG_CACHE'] = $cache;
        $_ENV['APP_CONFIG_CACHE'] = $cache;

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Tests refuse cached configuration.');

            (new class
            {
                use CreatesApplication;
            })->createApplication();
        } finally {
            unlink($cache);

            if ($previousServer === null) {
                unset($_SERVER['APP_CONFIG_CACHE']);
            } else {
                $_SERVER['APP_CONFIG_CACHE'] = $previousServer;
            }

            if ($previousEnvironment === null) {
                unset($_ENV['APP_CONFIG_CACHE']);
            } else {
                $_ENV['APP_CONFIG_CACHE'] = $previousEnvironment;
            }
        }
    }

    public function test_ci_uses_an_isolated_mysql_service_for_the_documented_php_versions(): void
    {
        $workflow = $this->workflow('laravel-tests.yml');
        $job = $workflow['jobs']['tests'];
        $service = $job['services']['mysql'];
        $this->assertSame('mysql:8.0', $service['image']);
        $this->assertSame('testing', $service['env']['MYSQL_DATABASE']);
        $this->assertSame($job['env']['DB_DATABASE'], $service['env']['MYSQL_DATABASE']);
        $this->assertSame($job['env']['DB_USERNAME'], $service['env']['MYSQL_USER']);
        $this->assertNotSame('root', $job['env']['DB_USERNAME']);
        $this->assertSame($job['env']['DB_PASSWORD'], $service['env']['MYSQL_PASSWORD']);
        $this->assertStringNotContainsString('GRANT ALL ON *.*', (string) file_get_contents($this->path('.github/workflows/laravel-tests.yml')));
        $this->assertContains('8.4', $job['strategy']['matrix']['php']);
        $this->assertContains('8.5', $job['strategy']['matrix']['php']);

        $commands = implode("\n", array_column($job['steps'], 'run'));
        $this->assertStringContainsString('cp .env.testing .env', $commands);
        $this->assertStringContainsString('php artisan test --compact', $commands);
        $this->assertStringContainsString('composer install --no-dev', $commands);

        foreach (['config:cache', 'route:cache', 'view:cache'] as $command) {
            $this->assertStringContainsString('php artisan '.$command, $commands);
        }
    }

    public function test_pull_request_validation_is_read_only_and_actions_are_pinned(): void
    {
        $workflow = $this->workflow('laravel-tests.yml');
        $this->assertArrayHasKey('pull_request', $workflow['on']);
        $this->assertEmpty($workflow['on']['pull_request']);
        $this->assertArrayNotHasKey('pull_request_target', $workflow['on']);
        $this->assertSame(['contents' => 'read'], $workflow['permissions']);

        foreach (['laravel-tests.yml', 'label.yml'] as $file) {
            foreach ($this->workflow($file)['jobs'] as $job) {
                foreach ($job['steps'] as $step) {
                    if (!isset($step['uses'])) {
                        continue;
                    }

                    $this->assertMatchesRegularExpression('/^[\w.-]+\/[\w.-]+@[a-f0-9]{40}$/', $step['uses']);

                    if (str_starts_with($step['uses'], 'actions/checkout@')) {
                        $this->assertFalse($step['with']['persist-credentials']);
                    }
                }
            }
        }

        foreach ($this->workflow('label.yml')['jobs']['label']['steps'] as $step) {
            $this->assertArrayNotHasKey('run', $step, 'The privileged labeler must not execute pull-request code.');
            $this->assertStringStartsWith('actions/labeler@', $step['uses']);
        }
    }

    public function test_readme_retains_the_breaking_upgrade_warning_and_release_instructions(): void
    {
        $readme = (string) file_get_contents($this->path('README.md'));

        foreach (['Breaking change', 'data loss', 'V2 is deprecated', 'rollback', 'backup', 'CONTRIBUTING.md'] as $required) {
            $this->assertStringContainsString($required, $readme);
        }

        $this->assertLessThan(strpos($readme, 'artisan migrate'), strpos($readme, 'Breaking change'));
        $this->assertFileExists($this->path('CONTRIBUTING.md'));
    }

    private function environmentValue(string $file, string $name): string
    {
        $matched = preg_match('/^'.preg_quote($name, '/').'=(.*)$/m', (string) file_get_contents($this->path($file)), $matches);
        $this->assertSame(1, $matched, "Missing {$name} in {$file}.");

        return trim($matches[1]);
    }

    /**
     * @return array<string, mixed>
     */
    private function workflow(string $file): array
    {
        return Yaml::parseFile($this->path('.github/workflows/'.$file));
    }

    private function path(string $relative): string
    {
        return dirname(__DIR__, 2).'/'.$relative;
    }
}
