<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Models\Organization;
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
    private const MENU_PERMISSIONS = ['read student'];

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
            ->get('dashboard')
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

    public function test_the_sidebar_shows_the_academic_period_feature(): void
    {
        $this->authorized_user([
            'read admin',
            'read academic period',
            'create academic period',
        ])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('Academic Periods')
            ->assertSee('View academic periods')
            ->assertSee('Create academic period')
            ->assertSee(route('academic-periods.index'), false)
            ->assertDontSee('Semesters');
    }

    public function test_the_sidebar_shows_the_academic_period_result_sheet(): void
    {
        $this->authorized_user([
            'read admin',
            'read exam',
        ])
            ->get('dashboard/admins')
            ->assertOk()
            ->assertSee('Academic Period Result Sheet')
            ->assertSee(route('exams.academic-period-result-tabulation'), false)
            ->assertDontSee('Semester Result Sheet');
    }

    public function test_the_sidebar_shows_organizations_to_an_organization_administrator(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->nonMember();

        app(GrantOrganizationMembership::class)->grant($user, $organization);

        $this->actingAs($user)
            ->get(route('organizations.index'))
            ->assertOk()
            ->assertSee('Organizations')
            ->assertSee('View Organizations')
            ->assertSee(route('organizations.index'), false)
            ->assertDontSee(route('organizations.create'), false)
            ->assertSee("x-bind:class=\"{ '-rotate-90': !open }\"", false);
    }

    public function test_the_sidebar_hides_an_empty_multi_schools_section(): void
    {
        $html = $this->authorized_user(['read admin'])
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Multi Schools Management', $this->withoutLivewireSnapshots($html));
    }

    public function test_the_multi_schools_section_uses_the_schools_menu_permission(): void
    {
        $html = $this->authorized_user(['read admin', 'read school'])
            ->get('dashboard/admins')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Multi Schools Management', $this->withoutLivewireSnapshots($html));
        $this->assertStringContainsString(route('schools.index'), $html);
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

    /**
     * Remove component state, which contains menu labels even when Blade hides them.
     */
    private function withoutLivewireSnapshots(string $html): string
    {
        return preg_replace('/wire:snapshot="[^"]*"/', '', $html) ?? $html;
    }
}
