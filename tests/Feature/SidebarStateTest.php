<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarStateTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    /**
     * Permissions that render the Students submenu in the sidebar.
     *
     * @var list<string>
     */
    private const MENU_PERMISSIONS = ['read admin', 'header-administrate', 'menu-student', 'read student'];

    public function test_the_sidebar_opens_when_nothing_is_stored(): void
    {
        $this->authorized_user(['read admin'])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="expanded"', false)
            ->assertSee('sidebar(true)', false);
    }

    public function test_the_sidebar_reopens_in_the_state_it_was_left_in(): void
    {
        $this->authorized_user(['read admin'])
            ->withUnencryptedCookie('sidebar_state', 'false')
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="collapsed"', false)
            ->assertSee('data-collapsible="icon"', false)
            ->assertSee('sidebar(false)', false);
    }

    public function test_a_stored_open_sidebar_still_opens(): void
    {
        $this->authorized_user(['read admin'])
            ->withUnencryptedCookie('sidebar_state', 'true')
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('data-state="expanded"', false);
    }

    public function test_a_closed_submenu_stays_hidden_until_alpine_starts(): void
    {
        $html = $this->authorized_user(self::MENU_PERMISSIONS)
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        foreach ($this->submenuPanels($html) as $panel) {
            $this->assertStringContainsString('x-cloak', $panel, 'A closed submenu must be cloaked.');
        }
    }

    public function test_an_open_submenu_paints_straight_away(): void
    {
        $html = $this->authorized_user(self::MENU_PERMISSIONS)
            ->get('dashboard/students')
            ->assertOk()
            ->getContent();

        $panels = $this->submenuPanels($html);

        $this->assertNotEmpty($panels);
        foreach ($panels as $panel) {
            $this->assertStringNotContainsString('x-cloak', $panel, 'The open submenu must not be cloaked.');
        }
    }

    /**
     * Pull out the opening tag of every collapsible submenu panel.
     *
     * @return list<string>
     */
    private function submenuPanels(string $html): array
    {
        preg_match_all('/<div x-show="open" x-collapse[^>]*>/', $html, $matches);

        return $matches[0];
    }
}
