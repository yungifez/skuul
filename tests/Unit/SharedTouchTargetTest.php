<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The controls every screen shares must be at least 44px tall on a phone.
 *
 * They sit on every page, so one small target is missed on every page. The
 * sidebar trigger is the only way to reach the menu on a phone.
 */
class SharedTouchTargetTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function sharedControls(): array
    {
        return [
            'sidebar trigger' => ['livewire/layouts/header.blade.php', '<april:sidebar-trigger class="size-11'],
            'theme menu' => ['livewire/layouts/header.blade.php', 'aria-label="open theme selection" class="size-11'],
            'profile menu' => ['livewire/layouts/header.blade.php', 'class="ml-1 flex h-11'],
            'working year and term bar' => ['livewire/set-academic-period.blade.php', "'h-11 text-xs sm:h-8' => \$compact"],
            'working year and term page' => ['livewire/set-academic-period.blade.php', "'h-11 text-sm' => !\$compact"],
            'create action' => ['components/resource-create-action.blade.php', 'class="h-11 select-none'],
            'help button hit area' => ['components/help-tooltip.blade.php', 'before:-inset-2'],
        ];
    }

    #[DataProvider('sharedControls')]
    public function test_a_shared_control_is_large_enough_to_tap(string $view, string $expected): void
    {
        $source = (string) file_get_contents(dirname(__DIR__, 2).'/resources/views/'.$view);

        $this->assertStringContainsString($expected, $source);
    }
}
